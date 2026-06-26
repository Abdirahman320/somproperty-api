<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\DataBackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(private DataBackupService $backup) {}

    public function index()
    {
        $tables = $this->backup->tablesFor('admin');
        return view('admin.backup.index', compact('tables'));
    }

    public function export(Request $request)
    {
        $format = $request->validate(['format' => 'required|in:excel,csv,sql'])['format'];
        $stamp  = now()->format('Ymd_His');

        [$content, $filename, $mime] = match ($format) {
            'csv'   => [$this->backup->exportCsv('admin'),   "som_backup_admin_{$stamp}.csv",  'text/csv'],
            'sql'   => [$this->backup->exportSql('admin'),   "som_backup_admin_{$stamp}.sql",  'application/sql'],
            default => [$this->backup->exportExcel('admin'), "som_backup_admin_{$stamp}.xls",  'application/vnd.ms-excel'],
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
        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());

        try {
            $report = $this->backup->import($file->getRealPath(), $ext, 'admin');
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        $summary = $report['tables'] === null
            ? "{$report['rows']} SQL statement(s) executed."
            : "{$report['rows']} row(s) imported across {$report['tables']} table(s).";

        return back()->with('success', "Restore complete — {$summary}");
    }
}
