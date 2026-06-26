<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $q = AuditLog::latest();
        if ($from = $request->get('from')) $q->whereDate('created_at', '>=', $from);
        if ($to   = $request->get('to'))   $q->whereDate('created_at', '<=', $to);
        if ($a    = $request->get('action')) $q->where('action', 'like', "%$a%");
        $logs = $q->paginate(50);
        return response()->json(['success' => true, 'data' => $logs->items(),
            'meta' => ['total' => $logs->total(), 'current_page' => $logs->currentPage(), 'last_page' => $logs->lastPage()]]);
    }
}

/* ────────────────────────────────────────────
   Advertisements (admin)
──────────────────────────────────────────── */
