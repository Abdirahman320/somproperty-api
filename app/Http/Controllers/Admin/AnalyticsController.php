<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, TenantBill, Tenant, Unit};
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller {
    public function index() {
        $monthlyMrr = collect(range(11,0))->map(function($i) {
            $month = Carbon::now()->subMonths($i)->startOfMonth();
            return [
                'label' => $month->format('M y'),
                'mrr'   => (float) Owner::where('status','active')
                    ->join('plans','plans.id','=','owners.plan_id')
                    ->sum('plans.price_monthly'),
            ];
        });
        $stats = [
            'total_owners'     => Owner::where('status','active')->count(),
            'total_tenants'    => Tenant::count(),
            'total_units'      => Unit::count(),
            'occupied_units'   => Unit::where('status','occupied')->count(),
            'monthly_rent'     => TenantBill::where('billing_month', now()->startOfMonth()->toDateString())->sum('amount_paid'),
            'overdue_amount'   => TenantBill::where('status','overdue')->sum('total_amount'),
        ];
        return view('admin.analytics', compact('stats','monthlyMrr'));
    }
    public function revenue() {
        $plans = \App\Models\Plan::withCount(['owners as active_owners' => fn($q) => $q->where('status','active')])->get();
        $mrr   = Owner::where('status','active')->join('plans','plans.id','=','owners.plan_id')->sum('plans.price_monthly');
        return view('admin.revenue', compact('plans','mrr'));
    }
}
