<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{TenantBill, Payment, Unit, Tenant};
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller {
    public function index(Request $request) {
        $owner = $request->owner;
        $year  = $request->get('year', now()->year);

        $monthlyRevenue = collect(range(1,12))->map(fn($m) => [
            'month'     => Carbon::create($year,$m,1)->format('M'),
            'rent'      => (float) TenantBill::where('owner_id',$owner->id)->whereYear('billing_month',$year)->whereMonth('billing_month',$m)->sum('amount_paid'),
            'water'     => (float) TenantBill::where('owner_id',$owner->id)->whereYear('billing_month',$year)->whereMonth('billing_month',$m)->sum('water_amount'),
            'electric'  => (float) TenantBill::where('owner_id',$owner->id)->whereYear('billing_month',$year)->whereMonth('billing_month',$m)->sum('electric_amount'),
        ]);

        $occupancyRate = $owner->units()->count() > 0
            ? round($owner->units()->where('status','occupied')->count() / $owner->units()->count() * 100, 1) : 0;

        $totals = [
            'annual_revenue' => TenantBill::where('owner_id',$owner->id)->whereYear('billing_month',$year)->sum('amount_paid'),
            'total_units'    => $owner->units()->count(),
            'occupied_units' => $owner->units()->where('status','occupied')->count(),
            'occupancy_rate' => $occupancyRate,
            'overdue_amount' => TenantBill::where('owner_id',$owner->id)->where('status','overdue')->sum('total_amount'),
        ];

        return view('owner.reports.index', compact('monthlyRevenue','totals','year'));
    }
}
