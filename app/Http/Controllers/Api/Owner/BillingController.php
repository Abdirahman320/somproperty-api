<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{TenantBill, Payment, UtilityReading};
use App\Services\BillingService;
use App\Jobs\SendBillingNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function __construct(private BillingService $billing) {}

    public function index(Request $request)
    {
        $owner = $request->user();
        $month = Carbon::parse($request->get('month', now()->format('Y-m-01')));

        $summary = $this->billing->getMonthlySummary($owner->id, $month);

        $bills = TenantBill::where('owner_id', $owner->id)
            ->where('billing_month', $month->toDateString())
            ->with(['tenant','unit.property'])
            ->latest()->paginate(50);

        $data = $bills->getCollection()->map(fn($b) => [
            'id'              => $b->id,
            'billing_month'   => $b->billing_month->format('Y-m-d'),
            'due_date'        => $b->due_date->format('Y-m-d'),
            'status'          => $b->status,
            'rent_amount'     => (float) $b->rent_amount,
            'water_amount'    => (float) $b->water_amount,
            'electric_amount' => (float) $b->electric_amount,
            'total_amount'    => (float) $b->total_amount,
            'amount_paid'     => (float) $b->amount_paid,
            'balance_due'     => (float) $b->balance_due,
            'notification_count'=> $b->notification_count,
            'notification_sent_at'=> $b->notification_sent_at,
            'tenant' => ['id'=>$b->tenant->id,'full_name'=>$b->tenant->full_name,'email'=>$b->tenant->email,'phone'=>$b->tenant->phone],
            'unit'   => ['unit_number'=>$b->unit->unit_number,'property_name'=>$b->unit->property->name],
        ]);

        return response()->json([
            'summary' => $summary,
            'bills'   => $data,
            'total'   => $bills->total(),
            'month'   => $month->format('F Y'),
        ]);
    }

    public function generate(Request $request)
    {
        $owner = $request->user();
        $month = Carbon::parse($request->get('month', now()->format('Y-m-01')));
        $count = $this->billing->generateMonthlyBills($owner->id, $month);
        return response()->json(['message' => "$count bills generated for {$month->format('F Y')}", 'count' => $count]);
    }

    public function notifyAll(Request $request)
    {
        $owner = $request->user();
        $month = Carbon::parse($request->get('month', now()->format('Y-m-01')));

        $bills = TenantBill::where('owner_id', $owner->id)
            ->where('billing_month', $month->toDateString())
            ->whereIn('status', ['pending','overdue','partially_paid'])
            ->get();

        foreach ($bills as $bill) {
            SendBillingNotification::dispatch($bill)->onQueue('emails');
        }

        return response()->json(['message' => "{$bills->count()} notifications queued.", 'count' => $bills->count()]);
    }

    public function notifySingle(Request $request, $billId)
    {
        $owner = $request->user();
        $bill  = TenantBill::where('owner_id', $owner->id)->findOrFail($billId);
        SendBillingNotification::dispatch($bill)->onQueue('emails');
        return response()->json(['message' => "Notification sent to {$bill->tenant->full_name}"]);
    }

    public function recordPayment(Request $request, $billId)
    {
        $owner = $request->user();
        $bill  = TenantBill::where('owner_id', $owner->id)->findOrFail($billId);

        $data = $request->validate([
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank_transfer,check,online,other',
            'reference_number' => 'nullable|string|max:100',
            'payment_date'     => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        $this->billing->recordPayment($bill, $data, $owner->id);

        return response()->json(['message' => 'Payment recorded.', 'balance_due' => (float) $bill->fresh()->balance_due]);
    }

    public function storeUtilityReading(Request $request)
    {
        $owner = $request->user();
        $data  = $request->validate([
            'unit_id'          => 'required|integer|exists:units,id',
            'utility_type'     => 'required|in:water,electric,gas',
            'reading_date'     => 'required|date',
            'reading_value'    => 'required|numeric|min:0',
            'rate_per_unit'    => 'required|numeric|min:0',
        ]);

        $reading = UtilityReading::create(['owner_id' => $owner->id, ...$data]);
        $this->billing->updateBillUtilities($owner->id, $data['unit_id'], Carbon::parse($data['reading_date'])->startOfMonth());

        return response()->json(['message' => 'Utility reading saved.', 'id' => $reading->id], 201);
    }

    public function show(Request $request, $billId)
    {
        $owner = $request->user();
        $bill  = TenantBill::where('owner_id', $owner->id)
            ->with(['tenant','unit.property','payments','contract'])
            ->findOrFail($billId);

        return response()->json(['data' => [
            'id'              => $bill->id,
            'billing_month'   => $bill->billing_month->format('Y-m-d'),
            'due_date'        => $bill->due_date->format('Y-m-d'),
            'status'          => $bill->status,
            'rent_amount'     => (float)$bill->rent_amount,
            'water_prev_reading'=> $bill->water_prev_reading,
            'water_curr_reading'=> $bill->water_curr_reading,
            'water_consumption' => $bill->water_consumption,
            'water_rate'        => $bill->water_rate,
            'water_amount'    => (float)$bill->water_amount,
            'electric_prev_reading'=> $bill->electric_prev_reading,
            'electric_curr_reading'=> $bill->electric_curr_reading,
            'electric_consumption' => $bill->electric_consumption,
            'electric_rate'        => $bill->electric_rate,
            'electric_amount' => (float)$bill->electric_amount,
            'total_amount'    => (float)$bill->total_amount,
            'amount_paid'     => (float)$bill->amount_paid,
            'balance_due'     => (float)$bill->balance_due,
            'tenant' => ['full_name'=>$bill->tenant->full_name,'email'=>$bill->tenant->email],
            'unit'   => ['unit_number'=>$bill->unit->unit_number,'property_name'=>$bill->unit->property->name],
            'payments'=> $bill->payments->map(fn($p)=>['amount'=>(float)$p->amount,'method'=>$p->payment_method,'date'=>$p->payment_date,'reference'=>$p->reference_number]),
        ]]);
    }
}
