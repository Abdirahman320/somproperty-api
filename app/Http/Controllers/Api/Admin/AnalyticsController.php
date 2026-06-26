<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = (int) $request->get('months', 12);
        $now = Carbon::now();
        $monthly = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->startOfMonth();
            $monthly[] = [
                'month'   => $m->format('M Y'),
                'key'     => $m->format('Y-m'),
                'new_owners' => Owner::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count(),
                'mrr'     => (float) Owner::where('status', 'active')
                    ->join('plans', 'owners.plan_id', '=', 'plans.id')
                    ->sum('plans.price_monthly'),
            ];
        }
        return response()->json(['success' => true, 'data' => ['monthly' => $monthly,
            'plan_breakdown' => Plan::withCount(['owners' => fn($q) => $q->where('status', 'active')])->get()
                ->map(fn($p) => ['plan' => $p->name, 'count' => $p->owners_count, 'mrr' => $p->owners_count * $p->price_monthly]),
        ]]);
    }

    public function revenue(Request $request)
    {
        return $this->index($request);
    }
}

/* ────────────────────────────────────────────
   Settings
──────────────────────────────────────────── */
