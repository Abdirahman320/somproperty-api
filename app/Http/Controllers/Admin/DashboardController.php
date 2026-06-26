<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Plan, TenantBill, Tenant, Unit, Contract, Property};
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller {
    public function index() {
        $from = Carbon::today();
        $to   = Carbon::today()->addDays(30);

        $stats = [
            'total_owners'      => Owner::where('status','active')->count(),
            'total_properties'  => Property::count(),
            'total_tenants'     => Tenant::count(),
            'mrr'               => Owner::where('status','active')->join('plans','plans.id','=','owners.plan_id')->sum('plans.price_monthly'),
            'trial_owners'      => Owner::where('status','trial')->count(),
            'suspended_owners'  => Owner::where('status','suspended')->count(),
            // System-wide rooms + revenue + expiring contracts
            'occupied'          => Unit::where('status','occupied')->count(),
            'vacant'            => Unit::where('status','vacant')->count(),
            'total_revenue'     => TenantBill::sum('amount_paid'),
            'expiring_count'    => Contract::where('status','active')->whereBetween('end_date', [$from, $to])->count(),
        ];

        $expiringContracts = Contract::where('status','active')
            ->whereBetween('end_date', [$from, $to])
            ->with(['owner','tenant','unit'])->orderBy('end_date')->take(10)->get();

        $recentOwners = Owner::with('plan')->latest()->take(8)->get();
        $planStats    = Plan::withCount(['owners'=>fn($q)=>$q->where('status','active')])->get();

        return view('admin.dashboard', compact('stats','expiringContracts','recentOwners','planStats'));
    }
}
