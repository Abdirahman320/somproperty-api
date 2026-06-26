<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditController extends Controller {
    public function index() {
        $logs = AuditLog::latest()->paginate(50);
        return view('admin.audit', compact('logs'));
    }
}
