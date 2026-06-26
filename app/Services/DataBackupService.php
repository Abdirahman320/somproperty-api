<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Backup (export) and restore (import) of application data.
 *
 * Two scopes:
 *   - 'admin'  : every data table, all rows.
 *   - 'owner'  : only rows belonging to a single owner (by owner_id / relationship).
 *
 * Three portable, dependency-free formats:
 *   - Excel : SpreadsheetML 2003 (.xls XML) — one worksheet per table.
 *   - CSV   : a single .csv with "# TABLE: <name>" section markers.
 *   - SQL   : INSERT statements wrapped with foreign-key-check toggles.
 */
class DataBackupService
{
    /** All data-bearing tables, in foreign-key-safe (parent-first) order. */
    public const ADMIN_TABLES = [
        'plans', 'admin_users', 'owners', 'properties', 'units', 'tenants',
        'contracts', 'billing_cycles', 'tenant_bills', 'payments',
        'utility_readings', 'notifications', 'tenant_notifications',
        'complaints', 'complaint_replies', 'assets', 'technical_issues',
        'advertisements', 'bookings', 'ad_billings', 'tenant_documents',
        'audit_logs',
    ];

    /** Owner-scoped tables (excludes platform tables like plans / admin_users). */
    public const OWNER_TABLES = [
        'owners', 'properties', 'units', 'tenants', 'contracts',
        'billing_cycles', 'tenant_bills', 'payments', 'utility_readings',
        'notifications', 'tenant_notifications', 'complaints',
        'complaint_replies', 'assets', 'technical_issues', 'advertisements',
        'bookings', 'ad_billings', 'tenant_documents', 'audit_logs',
    ];

    public function tablesFor(string $scope): array
    {
        return $scope === 'owner' ? self::OWNER_TABLES : self::ADMIN_TABLES;
    }

    /** Build a query for a table, scoped to an owner when needed. */
    protected function query(string $table, string $scope, ?int $ownerId)
    {
        $q = DB::table($table);
        if ($scope !== 'owner') {
            return $q;
        }
        // Owner scope: restrict every table to the owner's own data.
        if ($table === 'owners') {
            return $q->where('id', $ownerId);
        }
        if ($table === 'complaint_replies') {
            return $q->whereIn('complaint_id', function ($sub) use ($ownerId) {
                $sub->select('id')->from('complaints')->where('owner_id', $ownerId);
            });
        }
        if (Schema::hasColumn($table, 'owner_id')) {
            return $q->where('owner_id', $ownerId);
        }
        // No way to scope -> return an empty set to stay safe.
        return $q->whereRaw('1 = 0');
    }

    /** Pull all scoped data as [table => ['columns'=>[], 'rows'=>[[...]]]]. */
    public function collect(string $scope, ?int $ownerId = null): array
    {
        $out = [];
        foreach ($this->tablesFor($scope) as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $columns = Schema::getColumnListing($table);
            $rows = $this->query($table, $scope, $ownerId)->get()
                ->map(fn ($r) => (array) $r)->all();
            $out[$table] = ['columns' => $columns, 'rows' => $rows];
        }
        return $out;
    }

    /* ───────────────────────── EXPORT ───────────────────────── */

    public function exportCsv(string $scope, ?int $ownerId = null): string
    {
        $data = $this->collect($scope, $ownerId);
        $buf = "# SOM Property data backup (CSV)\r\n";
        $buf .= '# Scope: ' . $scope . ($ownerId ? " (owner #{$ownerId})" : '') . "\r\n";
        $buf .= '# Generated: ' . now()->toDateTimeString() . "\r\n";

        foreach ($data as $table => $info) {
            $buf .= "\r\n# TABLE: {$table}\r\n";
            $buf .= $this->csvRow($info['columns']);
            foreach ($info['rows'] as $row) {
                $line = [];
                foreach ($info['columns'] as $col) {
                    $line[] = $row[$col] ?? '';
                }
                $buf .= $this->csvRow($line);
            }
        }
        return $buf;
    }

    protected function csvRow(array $fields): string
    {
        $escaped = array_map(function ($v) {
            if ($v === null) {
                return '';
            }
            $v = (string) $v;
            if (preg_match('/[",\r\n]/', $v)) {
                $v = '"' . str_replace('"', '""', $v) . '"';
            }
            return $v;
        }, $fields);
        return implode(',', $escaped) . "\r\n";
    }

    public function exportSql(string $scope, ?int $ownerId = null): string
    {
        $data = $this->collect($scope, $ownerId);
        $driver = DB::getDriverName();
        $buf = "-- SOM Property data backup (SQL)\n";
        $buf .= '-- Scope: ' . $scope . ($ownerId ? " (owner #{$ownerId})" : '') . "\n";
        $buf .= '-- Generated: ' . now()->toDateTimeString() . "\n\n";
        $buf .= $driver === 'sqlite' ? "PRAGMA foreign_keys = OFF;\n\n" : "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($data as $table => $info) {
            if (empty($info['rows'])) {
                continue;
            }
            $cols = '`' . implode('`, `', $info['columns']) . '`';
            foreach ($info['rows'] as $row) {
                $vals = [];
                foreach ($info['columns'] as $col) {
                    $vals[] = $this->sqlValue($row[$col] ?? null);
                }
                $buf .= "REPLACE INTO `{$table}` ({$cols}) VALUES (" . implode(', ', $vals) . ");\n";
            }
            $buf .= "\n";
        }
        $buf .= $driver === 'sqlite' ? "PRAGMA foreign_keys = ON;\n" : "SET FOREIGN_KEY_CHECKS=1;\n";
        return $buf;
    }

    protected function sqlValue($v): string
    {
        if ($v === null) {
            return 'NULL';
        }
        if (is_int($v) || is_float($v)) {
            return (string) $v;
        }
        return DB::getPdo()->quote((string) $v);
    }

    /** Excel 2003 SpreadsheetML — one worksheet per table. Opens in Excel/LibreOffice. */
    public function exportExcel(string $scope, ?int $ownerId = null): string
    {
        $data = $this->collect($scope, $ownerId);
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
              . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";

        foreach ($data as $table => $info) {
            $xml .= '<Worksheet ss:Name="' . $this->xmlSheetName($table) . '"><Table>' . "\n";
            // header
            $xml .= '<Row>';
            foreach ($info['columns'] as $col) {
                $xml .= '<Cell><Data ss:Type="String">' . $this->xmlEsc($col) . '</Data></Cell>';
            }
            $xml .= "</Row>\n";
            // data
            foreach ($info['rows'] as $row) {
                $xml .= '<Row>';
                foreach ($info['columns'] as $col) {
                    $val = $row[$col] ?? null;
                    if ($val === null || $val === '') {
                        $xml .= '<Cell><Data ss:Type="String"></Data></Cell>';
                    } elseif (is_numeric($val) && !preg_match('/^0[0-9]+$/', (string) $val)) {
                        $xml .= '<Cell><Data ss:Type="Number">' . $this->xmlEsc((string) $val) . '</Data></Cell>';
                    } else {
                        $xml .= '<Cell><Data ss:Type="String">' . $this->xmlEsc((string) $val) . '</Data></Cell>';
                    }
                }
                $xml .= "</Row>\n";
            }
            $xml .= "</Table></Worksheet>\n";
        }
        $xml .= '</Workbook>';
        return $xml;
    }

    protected function xmlEsc(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    protected function xmlSheetName(string $name): string
    {
        // Excel sheet names: max 31 chars, no : \ / ? * [ ]
        $name = preg_replace('/[:\\\\\/?*\[\]]/', '_', $name);
        return $this->xmlEsc(substr($name, 0, 31));
    }

    /* ───────────────────────── IMPORT ───────────────────────── */

    /**
     * Restore from an uploaded file. Returns ['tables'=>n, 'rows'=>n, 'messages'=>[]].
     * For owner scope, every imported row is forced to the owner's id and only
     * owner tables are accepted.
     */
    public function import(string $path, string $extension, string $scope, ?int $ownerId = null): array
    {
        $ext = strtolower($extension);
        return match (true) {
            $ext === 'sql'                       => $this->importSql($path, $scope, $ownerId),
            in_array($ext, ['xls', 'xml'])       => $this->importExcel($path, $scope, $ownerId),
            $ext === 'csv'                       => $this->importCsv($path, $scope, $ownerId),
            default => throw new \RuntimeException("Unsupported file type: .{$ext}"),
        };
    }

    protected function allowedTables(string $scope): array
    {
        return $this->tablesFor($scope);
    }

    protected function withForeignKeysOff(callable $fn)
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        try {
            return $fn();
        } finally {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    /** Upsert rows into a table. Owner scope forces owner_id and blocks foreign owners. */
    protected function upsertRows(string $table, array $columns, array $rows, string $scope, ?int $ownerId, array &$report): void
    {
        if (!in_array($table, $this->allowedTables($scope), true)) {
            $report['messages'][] = "Skipped table '{$table}' (not allowed for this scope).";
            return;
        }
        if (!Schema::hasTable($table)) {
            $report['messages'][] = "Skipped table '{$table}' (does not exist).";
            return;
        }

        $valid = Schema::getColumnListing($table);
        $hasOwner = in_array('owner_id', $valid, true);
        $count = 0;

        foreach ($rows as $raw) {
            $row = [];
            foreach ($columns as $i => $col) {
                if (in_array($col, $valid, true)) {
                    $row[$col] = ($raw[$i] ?? null) === '' ? null : ($raw[$i] ?? null);
                }
            }
            if (empty($row)) {
                continue;
            }

            // Owner scope safety: stamp owner_id, reject cross-owner rows.
            if ($scope === 'owner') {
                if ($table === 'owners') {
                    if ((int) ($row['id'] ?? 0) !== (int) $ownerId) {
                        continue; // never let an owner overwrite another owner
                    }
                } elseif ($hasOwner) {
                    if (isset($row['owner_id']) && (int) $row['owner_id'] !== (int) $ownerId) {
                        continue;
                    }
                    $row['owner_id'] = $ownerId;
                }
            }

            if (isset($row['id']) && $row['id'] !== null && $row['id'] !== '') {
                DB::table($table)->updateOrInsert(['id' => $row['id']], $row);
            } else {
                DB::table($table)->insert($row);
            }
            $count++;
        }

        if ($count) {
            $report['tables']++;
            $report['rows'] += $count;
            $report['messages'][] = "Imported {$count} row(s) into '{$table}'.";
        }
    }

    protected function importCsv(string $path, string $scope, ?int $ownerId): array
    {
        $report = ['tables' => 0, 'rows' => 0, 'messages' => []];
        $fh = fopen($path, 'r');
        if (!$fh) {
            throw new \RuntimeException('Could not read the uploaded file.');
        }

        $this->withForeignKeysOff(function () use ($fh, $scope, $ownerId, &$report) {
            DB::transaction(function () use ($fh, $scope, $ownerId, &$report) {
                $current = null; $columns = []; $rows = [];
                $flush = function () use (&$current, &$columns, &$rows, $scope, $ownerId, &$report) {
                    if ($current && $columns) {
                        $this->upsertRows($current, $columns, $rows, $scope, $ownerId, $report);
                    }
                    $current = null; $columns = []; $rows = [];
                };
                // fgetcsv correctly reassembles quoted fields that span multiple lines.
                while (($rec = fgetcsv($fh)) !== false) {
                    if ($rec === null) {
                        continue;
                    }
                    // blank line
                    if (count($rec) === 1 && ($rec[0] === null || $rec[0] === '')) {
                        continue;
                    }
                    $first = (string) ($rec[0] ?? '');
                    if (str_starts_with($first, '# TABLE:')) {
                        $flush();
                        $current = trim(substr($first, 8));
                        $columns = [];
                        continue;
                    }
                    if (str_starts_with($first, '#')) {
                        continue; // comment / header line
                    }
                    if ($current === null) {
                        continue;
                    }
                    if (!$columns) {
                        $columns = $rec;
                    } else {
                        $rows[] = $rec;
                    }
                }
                $flush();
            });
        });
        fclose($fh);
        return $report;
    }

    protected function importExcel(string $path, string $scope, ?int $ownerId): array
    {
        $report = ['tables' => 0, 'rows' => 0, 'messages' => []];
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException('Could not read the uploaded file.');
        }
        $xml = @simplexml_load_string($content);
        if ($xml === false) {
            throw new \RuntimeException('The Excel/XML file could not be parsed. Export a fresh backup and try again.');
        }
        $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');

        $this->withForeignKeysOff(function () use ($xml, $scope, $ownerId, &$report) {
            DB::transaction(function () use ($xml, $scope, $ownerId, &$report) {
                foreach ($xml->Worksheet as $ws) {
                    $attrs = $ws->attributes('urn:schemas-microsoft-com:office:spreadsheet');
                    $table = (string) ($attrs['Name'] ?? '');
                    if (!$table) {
                        continue;
                    }
                    $columns = []; $rows = []; $first = true;
                    foreach ($ws->Table->Row as $r) {
                        $cells = [];
                        foreach ($r->Cell as $c) {
                            $cells[] = (string) $c->Data;
                        }
                        if ($first) {
                            $columns = $cells; $first = false;
                        } else {
                            $rows[] = $cells;
                        }
                    }
                    if ($columns) {
                        $this->upsertRows($table, $columns, $rows, $scope, $ownerId, $report);
                    }
                }
            });
        });
        return $report;
    }

    protected function importSql(string $path, string $scope, ?int $ownerId): array
    {
        $report = ['tables' => 0, 'rows' => 0, 'messages' => []];
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new \RuntimeException('Could not read the uploaded file.');
        }
        $statements = $this->splitSql($sql);
        $allowed = $this->allowedTables($scope);
        $executed = 0;

        // Owner scope: validate every statement before running anything.
        if ($scope === 'owner') {
            foreach ($statements as $st) {
                $st = trim($st);
                if ($st === '' || preg_match('/^(SET\s+FOREIGN_KEY_CHECKS|PRAGMA\s+foreign_keys)/i', $st)) {
                    continue; // control statements handled by the wrapper
                }
                if (!preg_match('/^\s*(INSERT|REPLACE)\s+INTO\s+`?(\w+)`?/i', $st, $m)) {
                    throw new \RuntimeException('Owner restore only accepts INSERT/REPLACE statements (use a backup exported from this account).');
                }
                $table = strtolower($m[2]);
                if (!in_array($table, $allowed, true)) {
                    throw new \RuntimeException("Statement references table '{$table}' which is not allowed for an owner restore.");
                }
            }
        }

        $this->withForeignKeysOff(function () use ($statements, &$executed, &$report) {
            DB::transaction(function () use ($statements, &$executed) {
                foreach ($statements as $st) {
                    $st = trim($st);
                    if ($st === '') {
                        continue;
                    }
                    // skip standalone FK-toggle / pragma lines (handled by wrapper)
                    if (preg_match('/^(SET\s+FOREIGN_KEY_CHECKS|PRAGMA\s+foreign_keys)/i', $st)) {
                        continue;
                    }
                    DB::statement($st);
                    $executed++;
                }
            });
        });

        $report['rows'] = $executed;
        $report['tables'] = null;
        $report['messages'][] = "Executed {$executed} SQL statement(s).";
        return $report;
    }

    /** Split a SQL dump into individual statements (handles quoted semicolons). */
    protected function splitSql(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inSingle = false; $inDouble = false; $len = strlen($sql);
        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            $prev = $i > 0 ? $sql[$i - 1] : '';
            if ($ch === "'" && !$inDouble && $prev !== '\\') {
                $inSingle = !$inSingle;
            } elseif ($ch === '"' && !$inSingle && $prev !== '\\') {
                $inDouble = !$inDouble;
            }
            if ($ch === ';' && !$inSingle && !$inDouble) {
                $statements[] = $buffer;
                $buffer = '';
            } else {
                $buffer .= $ch;
            }
        }
        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }
        // drop comment-only lines
        return array_filter($statements, function ($s) {
            $s = trim($s);
            return $s !== '' && !str_starts_with($s, '--');
        });
    }
}
