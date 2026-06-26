<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{TenantBill, Complaint, Contract};
use App\Services\BillingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller {
    public function index(Request $request) {
        $owner = $request->owner;
        $month = Carbon::now()->startOfMonth();
        $from  = Carbon::today();
        $to    = Carbon::today()->addDays(30);

        $totalUnits = $owner->units()->whereNotIn('status',['disposed'])->count();
        $occupied   = $owner->units()->where('status','occupied')->count();
        $vacant     = $owner->units()->where('status','vacant')->count();

        $stats = [
            'total_units'    => $totalUnits,
            'plan_limit'     => $owner->max_apartments,
            'occupied'       => $occupied,
            'vacant'         => $vacant,
            'occupancy_rate' => $totalUnits > 0 ? round($occupied / $totalUnits * 100, 1) : 0,
            'rent_collected' => TenantBill::where('owner_id',$owner->id)->where('billing_month',$month)->sum('amount_paid'),
            'total_revenue'  => TenantBill::where('owner_id',$owner->id)->sum('amount_paid'),
            'overdue_count'  => TenantBill::where('owner_id',$owner->id)->where('status','overdue')->count(),
            'expiring_count' => Contract::where('owner_id',$owner->id)->where('status','active')
                                    ->whereBetween('end_date', [$from, $to])->count(),
        ];

        $expiringContracts = Contract::where('owner_id',$owner->id)->where('status','active')
            ->whereBetween('end_date', [$from, $to])
            ->with(['tenant','unit'])->orderBy('end_date')->take(8)->get();

        $recentActivity = \App\Models\AuditLog::where('owner_id',$owner->id)->latest()->take(6)->get();
        $billing = new BillingService();
        $chartLabels = $billing->getRevenueChartLabels($owner->id);
        $chartData   = $billing->getRevenueChartData($owner->id);
        $openComplaints = Complaint::where('owner_id',$owner->id)->where('status','open')->count();

        return view('owner.dashboard', compact('stats','expiringContracts','recentActivity','chartLabels','chartData','openComplaints'));
    }
}
