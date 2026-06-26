<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function __construct(private DataBackupService $backup) {}

    public function export(Request $request)
    {
        $format = $request->validate(['format' => 'required|in:excel,csv,sql'])['format'];
        $stamp  = now()->format('Ymd_His');
        [$content, $filename, $mime] = match ($format) {
            'csv'   => [$this->backup->exportCsv('admin'),   "som_admin_{$stamp}.csv", 'text/csv'],
            'sql'   => [$this->backup->exportSql('admin'),   "som_admin_{$stamp}.sql", 'application/sql'],
            default => [$this->backup->exportExcel('admin'), "som_admin_{$stamp}.xls", 'application/vnd.ms-excel'],
        };
        return response($content, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|extensions:csv,txt,sql,xls,xml|max:51200']);
        $file = $request->file('file');
        try {
            $report = $this->backup->import($file->getRealPath(), strtolower($file->getClientOriginalExtension()), 'admin');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()], 422);
        }
        $summary = $report['tables'] === null ? "{$report['rows']} SQL statements executed."
            : "{$report['rows']} rows imported across {$report['tables']} table(s).";
        return response()->json(['success' => true, 'message' => $summary, 'data' => $report]);
    }
}
