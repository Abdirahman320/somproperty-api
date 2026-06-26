<?php
namespace App\Http\Controllers\Tenant;
use App\Http\Controllers\Controller;
use App\Models\TenantBill;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingController extends Controller {
    public function index(Request $request) {
        $tenant = $request->tenant;
        $currentBill  = TenantBill::where('tenant_id',$tenant->id)->latest('billing_month')->first();
        $pastBills    = TenantBill::where('tenant_id',$tenant->id)->latest('billing_month')->skip(1)->take(12)->get();
        $unreadCount  = $tenant->notifications()->where('is_read',false)->count();
        return view('tenant.billing.index', compact('tenant','currentBill','pastBills','unreadCount'));
    }
    public function show(Request $request, TenantBill $bill) {
        abort_if($bill->tenant_id !== $request->tenant->id, 403);
        $bill->load(['unit.property','owner','payments']);
        $unreadCount = $request->tenant->notifications()->where('is_read',false)->count();
        return view('tenant.billing.show', compact('bill','unreadCount'));
    }
    public function pdf(Request $request, TenantBill $bill) {
        abort_if($bill->tenant_id !== $request->tenant->id, 403);
        $bill->load(['tenant','unit.property','owner']);
        $pdf = Pdf::loadView('pdf.bill', ['bill'=>$bill]);
        return $pdf->download("bill_{$bill->billing_month->format('Y-m')}.pdf");
    }
}
