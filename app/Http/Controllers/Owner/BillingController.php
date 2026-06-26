<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\TenantBill;
use App\Models\BillingCycle;
use App\Models\UtilityReading;
use App\Models\Unit;
use App\Services\BillingService;
use App\Services\GmailService;
use App\Jobs\SendBillingNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingController extends Controller
{
    public function __construct(
        protected BillingService $billing,
        protected GmailService   $gmail
    ) {}

    /* ── List bills for current month ── */
    public function index(Request $request)
    {
        $owner = $request->owner;
        $month = Carbon::now()->startOfMonth();

        $summary = $this->billing->getMonthlySummary($owner->id, $month);
        $bills   = TenantBill::where('owner_id', $owner->id)
            ->where('billing_month', $month)
            ->with(['tenant', 'unit.property'])
            ->orderBy('status')
            ->paginate(25);

        $chartLabels = $this->billing->getRevenueChartLabels($owner->id);
        $chartData   = $this->billing->getRevenueChartData($owner->id);

        return view('owner.billing.index', compact('summary', 'bills', 'chartLabels', 'chartData'));
    }

    /* ── Generate bills for current month ── */
    public function generate(Request $request)
    {
        $owner  = $request->owner;
        $month  = Carbon::now()->startOfMonth();
        $count  = $this->billing->generateMonthlyBills($owner->id, $month);

        return redirect()->route('owner.billing.index')
            ->with('success', "{$count} bills generated for " . $month->format('F Y'));
    }

    /* ── Show utility readings entry form ── */
    public function createUtility(Request $request)
    {
        $owner = $request->owner;
        $units = Unit::where('owner_id', $owner->id)
            ->whereIn('status', ['occupied', 'reserved'])
            ->with('property')
            ->orderBy('unit_number')
            ->get();

        return view('owner.billing.utility.create', compact('units'));
    }

    /* ── Record utility readings ── */
    public function storeUtility(Request $request)
    {
        $validated = $request->validate([
            'unit_id'       => 'required|integer',
            'billing_month' => 'required|date',
            'water_prev'    => 'nullable|numeric|min:0',
            'water_curr'    => 'nullable|numeric|min:0',
            'water_rate'    => 'nullable|numeric|min:0',
            'electric_prev' => 'nullable|numeric|min:0',
            'electric_curr' => 'nullable|numeric|min:0',
            'electric_rate' => 'nullable|numeric|min:0',
        ]);

        $owner = $request->owner;

        if (!empty($validated['water_curr'])) {
            UtilityReading::create([
                'owner_id'     => $owner->id,
                'unit_id'      => $validated['unit_id'],
                'utility_type' => 'water',
                'reading_date' => $validated['billing_month'],
                'reading_value'=> $validated['water_curr'],
                'rate_per_unit'=> $validated['water_rate'],
            ]);
        }

        if (!empty($validated['electric_curr'])) {
            UtilityReading::create([
                'owner_id'     => $owner->id,
                'unit_id'      => $validated['unit_id'],
                'utility_type' => 'electric',
                'reading_date' => $validated['billing_month'],
                'reading_value'=> $validated['electric_curr'],
                'rate_per_unit'=> $validated['electric_rate'],
            ]);
        }

        $this->billing->updateBillUtilities($owner->id, $validated['unit_id'], $validated['billing_month']);

        return back()->with('success', 'Utility readings saved and bill updated.');
    }

    /* ── Send notification for single bill ── */
    public function notify(Request $request, TenantBill $bill)
    {
        $this->authorise($request->owner, $bill);
        SendBillingNotification::dispatch($bill)->onQueue('emails');

        return back()->with('success', "Notification sent to {$bill->tenant->full_name}.");
    }

    /* ── Send notifications to all tenants ── */
    public function notifyAll(Request $request)
    {
        $owner  = $request->owner;
        $month  = Carbon::now()->startOfMonth();
        $bills  = TenantBill::where('owner_id', $owner->id)
            ->where('billing_month', $month)
            ->whereIn('status', ['pending', 'overdue', 'partially_paid'])
            ->with('tenant')
            ->get();

        $count = 0;
        foreach ($bills as $bill) {
            SendBillingNotification::dispatch($bill)->onQueue('emails');
            $count++;
        }

        return back()->with('success', "{$count} billing notifications queued for delivery.");
    }

    /* ── Show payment entry form ── */
    public function showPay(Request $request, TenantBill $bill)
    {
        $this->authorise($request->owner, $bill);
        $bill->load(['tenant', 'unit.property', 'payments']);
        return view('owner.billing.pay', compact('bill'));
    }

    /* ── Record payment ── */
    public function recordPayment(Request $request, TenantBill $bill)
    {
        $this->authorise($request->owner, $bill);

        $validated = $request->validate([
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank_transfer,check,online,other',
            'reference_number' => 'nullable|string|max:100',
            'payment_date'     => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        $this->billing->recordPayment($bill, $validated, $request->owner->id);

        return back()->with('success', 'Payment recorded successfully.');
    }

    /* ── PDF export ── */
    public function pdf(Request $request, TenantBill $bill)
    {
        $this->authorise($request->owner, $bill);
        $bill->load(['tenant', 'unit.property', 'contract', 'owner']);

        $pdf = Pdf::loadView('pdf.bill', ['bill' => $bill]);
        return $pdf->download("bill_{$bill->tenant->full_name}_{$bill->billing_month->format('Y-m')}.pdf");
    }

    /* ── Show single bill ── */
    public function show(Request $request, TenantBill $bill)
    {
        $this->authorise($request->owner, $bill);
        $bill->load(['tenant', 'unit.property', 'contract', 'payments']);
        return view('owner.billing.show', compact('bill'));
    }

    /* ── Auth check ── */
    private function authorise($owner, TenantBill $bill): void
    {
        abort_if($bill->owner_id !== $owner->id, 403);
    }
}
