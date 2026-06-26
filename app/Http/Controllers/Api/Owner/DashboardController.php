<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{TenantBill, Complaint};
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $owner = $request->user();   // Sanctum resolves owner
        $month = Carbon::now()->startOfMonth();

        $totalUnits     = $owner->units()->whereNotIn('status',['disposed'])->count();
        $occupied       = $owner->units()->where('status','occupied')->count();
        $vacant         = $owner->units()->where('status','vacant')->count();
        $totalProperties= $owner->properties()->count();
        $totalTenants   = \App\Models\Tenant::where('owner_id',$owner->id)->where('status','active')->count();
        $rentCollected  = TenantBill::where('owner_id',$owner->id)
            ->where('billing_month', $month->toDateString())
            ->sum('amount_paid');
        $overdueCount   = TenantBill::where('owner_id',$owner->id)
            ->where('status','overdue')->count();
        $openComplaints = Complaint::where('owner_id',$owner->id)
            ->where('status','open')->count();

        // Revenue chart — last 7 months
        $chart = collect(range(6,0))->map(function($i) use ($owner) {
            $m = Carbon::now()->subMonths($i)->startOfMonth();
            return [
                'label'  => $m->format('M'),
                'amount' => (float) TenantBill::where('owner_id',$owner->id)
                    ->where('billing_month',$m->toDateString())
                    ->sum('amount_paid'),
            ];
        });

        // Recent activity (last 8 audit logs)
        $activity = \App\Models\AuditLog::where('owner_id',$owner->id)
            ->latest()->take(8)->get()
            ->map(fn($a) => [
                'description' => $a->action,
                'time'        => $a->created_at->diffForHumans(),
                'badge'       => 'info',
            ]);

        return response()->json([
            'stats' => [
                'total_units'      => $totalUnits,
                'total_properties' => $totalProperties,
                'total_tenants'    => $totalTenants,
                'vacant'           => $vacant,
                'plan_limit'       => $owner->max_apartments,
                'occupied'         => $occupied,
                'occupancy_rate'   => $totalUnits > 0 ? round($occupied/$totalUnits*100,1) : 0,
                'rent_collected'   => (float) $rentCollected,
                'overdue_count'    => $overdueCount,
                'open_complaints'  => $openComplaints,
            ],
            'chart'           => $chart,
            'recent_activity' => $activity,
            'user'            => [
                'id'             => $owner->id,
                'full_name'      => $owner->full_name,
                'email'          => $owner->email,
                'company_name'   => $owner->company_name,
                'max_apartments' => $owner->max_apartments,
                'plan'           => ['name' => $owner->plan->name, 'price_monthly' => $owner->plan->price_monthly],
            ],
        ]);
    }
}
