<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Property, Unit, Tenant, TenantBill, Contract, Complaint, Advertisement};
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $month = $now->copy()->startOfMonth();

        $mrr = Owner::where('status', 'active')
            ->join('plans', 'owners.plan_id', '=', 'plans.id')
            ->sum('plans.price_monthly');

        $monthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->startOfMonth();
            $monthly[] = [
                'month' => $m->format('M Y'),
                'mrr'   => (float) Owner::where('status', 'active')
                    ->whereDate('subscription_starts_at', '<=', $m->copy()->endOfMonth())
                    ->join('plans', 'owners.plan_id', '=', 'plans.id')
                    ->sum('plans.price_monthly'),
            ];
        }

        $ownerStatuses = Owner::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        $expiringSubs = Owner::where('status', 'active')
            ->whereNotNull('subscription_ends_at')
            ->whereDate('subscription_ends_at', '>=', $now)
            ->whereDate('subscription_ends_at', '<=', $now->copy()->addDays(30))
            ->with('plan')->latest('subscription_ends_at')->limit(5)->get()
            ->map(fn($o) => ['id' => $o->id, 'full_name' => $o->full_name, 'email' => $o->email,
                'subscription_ends_at' => $o->subscription_ends_at, 'plan' => $o->plan?->name]);

        $recentOwners = Owner::with('plan')->latest()->limit(5)->get()
            ->map(fn($o) => ['id' => $o->id, 'full_name' => $o->full_name, 'company_name' => $o->company_name,
                'status' => $o->status, 'plan' => $o->plan?->name, 'created_at' => $o->created_at]);

        return response()->json(['success' => true, 'data' => [
            'kpi' => [
                'total_owners'        => Owner::count(),
                'active_owners'       => Owner::where('status', 'active')->count(),
                'trial_owners'        => Owner::where('status', 'trial')->count(),
                'suspended_owners'    => Owner::where('status', 'suspended')->count(),
                'mrr'                 => (float) $mrr,
                'total_properties'    => Property::count(),
                'total_units'         => Unit::count(),
                'vacant_units'        => Unit::where('status', 'vacant')->count(),
                'active_tenants'      => Tenant::where('status', 'active')->count(),
                'open_complaints'     => Complaint::whereIn('status', ['open', 'in_progress'])->count(),
                'active_ads'          => Advertisement::where('is_published', true)->count(),
            ],
            'monthly_mrr'       => $monthly,
            'owner_statuses'    => $ownerStatuses,
            'expiring_subs'     => $expiringSubs,
            'recent_owners'     => $recentOwners,
        ]]);
    }
}
