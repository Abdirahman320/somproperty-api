<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Services\DataBackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(private DataBackupService $backup) {}

    public function index(Request $request)
    {
        $tables = $this->backup->tablesFor('owner');
        return view('owner.backup.index', compact('tables'));
    }

    public function export(Request $request)
    {
        $ownerId = $request->owner->id;
        $format  = $request->validate(['format' => 'required|in:excel,csv,sql'])['format'];
        $stamp   = now()->format('Ymd_His');

        [$content, $filename, $mime] = match ($format) {
            'csv'   => [$this->backup->exportCsv('owner', $ownerId),   "som_backup_owner{$ownerId}_{$stamp}.csv", 'text/csv'],
            'sql'   => [$this->backup->exportSql('owner', $ownerId),   "som_backup_owner{$ownerId}_{$stamp}.sql", 'application/sql'],
            default => [$this->backup->exportExcel('owner', $ownerId), "som_backup_owner{$ownerId}_{$stamp}.xls", 'application/vnd.ms-excel'],
        };

        return response($content, 200, [
            'Content-Type'        => $mime . '; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:csv,txt,sql,xls,xml|max:51200',
        ]);
        $ownerId = $request->owner->id;
        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());

        try {
            $report = $this->backup->import($file->getRealPath(), $ext, 'owner', $ownerId);
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        $summary = $report['tables'] === null
            ? "{$report['rows']} SQL statement(s) executed."
            : "{$report['rows']} row(s) imported across {$report['tables']} table(s).";

        return back()->with('success', "Restore complete — {$summary}");
    }
}
