<?php
namespace App\Http\Controllers\Api\Tenant;
use App\Http\Controllers\Controller;
use App\Models\TenantBill;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user();
        $bills  = TenantBill::where('tenant_id', $tenant->id)
            ->with(['unit.property','payments'])
            ->latest('billing_month')
            ->paginate(24);

        $data = $bills->getCollection()->map(fn($b) => [
            'id'              => $b->id,
            'billing_month'   => $b->billing_month->format('Y-m-d'),
            'due_date'        => $b->due_date->format('Y-m-d'),
            'status'          => $b->status,
            'rent_amount'     => (float)$b->rent_amount,
            'water_amount'    => (float)$b->water_amount,
            'water_consumption'=> $b->water_consumption,
            'water_rate'      => $b->water_rate,
            'electric_amount' => (float)$b->electric_amount,
            'electric_consumption'=> $b->electric_consumption,
            'electric_rate'   => $b->electric_rate,
            'late_fee'        => (float)$b->late_fee,
            'total_amount'    => (float)$b->total_amount,
            'amount_paid'     => (float)$b->amount_paid,
            'balance_due'     => (float)$b->balance_due,
            'unit_number'     => $b->unit->unit_number,
            'property_name'   => $b->unit->property->name,
            'payments'        => $b->payments->map(fn($p) => [
                'amount'          => (float)$p->amount,
                'payment_method'  => $p->payment_method,
                'payment_date'    => $p->payment_date,
                'reference_number'=> $p->reference_number,
            ]),
        ]);

        return response()->json(['data' => $data, 'total' => $bills->total()]);
    }

    public function show(Request $request, $id)
    {
        $tenant = $request->user();
        $bill   = TenantBill::where('tenant_id', $tenant->id)
            ->with(['unit.property','payments'])
            ->findOrFail($id);

        return response()->json(['data' => [
            'id'                   => $bill->id,
            'billing_month'        => $bill->billing_month->format('F Y'),
            'due_date'             => $bill->due_date->format('Y-m-d'),
            'status'               => $bill->status,
            'rent_amount'          => (float)$bill->rent_amount,
            'water_prev_reading'   => $bill->water_prev_reading,
            'water_curr_reading'   => $bill->water_curr_reading,
            'water_consumption'    => $bill->water_consumption,
            'water_rate'           => $bill->water_rate,
            'water_amount'         => (float)$bill->water_amount,
            'electric_prev_reading'=> $bill->electric_prev_reading,
            'electric_curr_reading'=> $bill->electric_curr_reading,
            'electric_consumption' => $bill->electric_consumption,
            'electric_rate'        => $bill->electric_rate,
            'electric_amount'      => (float)$bill->electric_amount,
            'late_fee'             => (float)$bill->late_fee,
            'discount_amount'      => (float)$bill->discount_amount,
            'total_amount'         => (float)$bill->total_amount,
            'amount_paid'          => (float)$bill->amount_paid,
            'balance_due'          => (float)$bill->balance_due,
            'unit_number'          => $bill->unit->unit_number,
            'property_name'        => $bill->unit->property->name,
            'payments'             => $bill->payments->map(fn($p) => [
                'amount'           => (float)$p->amount,
                'payment_method'   => $p->payment_method,
                'payment_date'     => $p->payment_date->format('Y-m-d'),
                'reference_number' => $p->reference_number,
            ]),
        ]]);
    }
}
