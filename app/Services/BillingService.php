<?php
namespace App\Services;
use App\Models\{TenantBill, BillingCycle, Contract, UtilityReading, Payment};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateMonthlyBills(int $ownerId, Carbon $month): int
    {
        $contracts = Contract::where('owner_id', $ownerId)->where('status','active')
            ->where('start_date', '<=', $month->copy()->endOfMonth())
            ->where('end_date',   '>=', $month->copy()->startOfMonth())
            ->with(['tenant','unit'])->get();

        $cycle = BillingCycle::firstOrCreate(
            ['owner_id'=>$ownerId,'property_id'=>null,'billing_month'=>$month->toDateString()],
            ['status'=>'draft']
        );

        $count = 0;
        foreach ($contracts as $contract) {
            if (TenantBill::where('owner_id',$ownerId)->where('contract_id',$contract->id)->where('billing_month',$month->toDateString())->exists()) continue;

            $dueDate = $month->copy()->day($contract->payment_due_day);
            if ($dueDate->isPast()) $dueDate->addMonth();

            $waterData    = $this->getUtilityReading($ownerId, $contract->unit_id, $month, 'water');
            $electricData = $this->getUtilityReading($ownerId, $contract->unit_id, $month, 'electric');

            $waterAmount    = !empty($waterData)    ? round($waterData['consumption']    * $waterData['rate'],    2) : 0;
            $electricAmount = !empty($electricData) ? round($electricData['consumption'] * $electricData['rate'], 2) : 0;

            TenantBill::create([
                'owner_id'              => $ownerId,
                'billing_cycle_id'      => $cycle->id,
                'contract_id'           => $contract->id,
                'tenant_id'             => $contract->tenant_id,
                'unit_id'               => $contract->unit_id,
                'billing_month'         => $month->toDateString(),
                'due_date'              => $dueDate->toDateString(),
                'rent_amount'           => $contract->monthly_rent,
                'water_prev_reading'    => $waterData['prev']        ?? null,
                'water_curr_reading'    => $waterData['curr']        ?? null,
                'water_consumption'     => $waterData['consumption'] ?? null,
                'water_rate'            => $waterData['rate']        ?? null,
                'water_amount'          => $waterAmount,
                'electric_prev_reading' => $electricData['prev']        ?? null,
                'electric_curr_reading' => $electricData['curr']        ?? null,
                'electric_consumption'  => $electricData['consumption'] ?? null,
                'electric_rate'         => $electricData['rate']        ?? null,
                'electric_amount'       => $electricAmount,
                'total_amount'          => $contract->monthly_rent + $waterAmount + $electricAmount,
                'status'                => 'pending',
            ]);
            $count++;
        }
        return $count;
    }

    public function recordPayment(TenantBill $bill, array $data, int $recordedBy): void
    {
        DB::transaction(function () use ($bill, $data, $recordedBy) {
            Payment::create([
                'owner_id'         => $bill->owner_id,
                'tenant_bill_id'   => $bill->id,
                'tenant_id'        => $bill->tenant_id,
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date'     => $data['payment_date'],
                'recorded_by'      => $recordedBy,
                'notes'            => $data['notes'] ?? null,
            ]);
            $totalPaid = $bill->payments()->sum('amount') + $data['amount'];
            $status    = $totalPaid >= $bill->total_amount ? 'paid' : 'partially_paid';
            $bill->update(['amount_paid' => $totalPaid, 'status' => $status]);
        });
    }

    /** Re-calculate utility amounts on a bill after new readings are saved */
    public function updateBillUtilities(int $ownerId, int $unitId, Carbon $month): void
    {
        $bill = TenantBill::where('owner_id', $ownerId)->where('unit_id', $unitId)->where('billing_month', $month->toDateString())->first();
        if (!$bill) return;

        $waterData    = $this->getUtilityReading($ownerId, $unitId, $month, 'water');
        $electricData = $this->getUtilityReading($ownerId, $unitId, $month, 'electric');
        $wa = !empty($waterData)    ? round($waterData['consumption']    * $waterData['rate'],    2) : $bill->water_amount;
        $ea = !empty($electricData) ? round($electricData['consumption'] * $electricData['rate'], 2) : $bill->electric_amount;

        $bill->update([
            'water_amount'    => $wa,
            'electric_amount' => $ea,
            'total_amount'    => $bill->rent_amount + $wa + $ea,
        ]);
    }

    public function getMonthlySummary(int $ownerId, Carbon $month): array
    {
        $bills       = TenantBill::where('owner_id',$ownerId)->where('billing_month',$month->toDateString())->get();
        $total       = $bills->count();
        $paid        = $bills->where('status','paid')->count();
        $totalRent   = $bills->sum('rent_amount');
        $totalWater  = $bills->sum('water_amount');
        $totalElec   = $bills->sum('electric_amount');
        $outstanding = $bills->sum('total_amount') - $bills->sum('amount_paid');

        return [
            'total_tenants'    => $total,
            'rent_paid_count'  => $paid,
            'rent_paid_pct'    => $total > 0 ? round($paid/$total*100) : 0,
            'total_rent'       => $totalRent,
            'rent_outstanding' => $outstanding,
            'total_water'      => $totalWater,
            'total_electric'   => $totalElec,
            'total_utilities'  => $totalWater + $totalElec,
            'util_paid_pct'    => $total > 0 ? round($paid/$total*100) : 0,
            'overdue_count'    => $bills->where('status','overdue')->count(),
        ];
    }

    private function getUtilityReading(int $ownerId, int $unitId, Carbon $month, string $type): array
    {
        $curr = UtilityReading::where('owner_id',$ownerId)->where('unit_id',$unitId)->where('utility_type',$type)
            ->whereYear('reading_date',$month->year)->whereMonth('reading_date',$month->month)
            ->orderByDesc('reading_date')->first();
        $prev = UtilityReading::where('owner_id',$ownerId)->where('unit_id',$unitId)->where('utility_type',$type)
            ->where('reading_date','<',$month->toDateString())->orderByDesc('reading_date')->first();
        if (!$curr) return [];
        $consumption = max(0, $curr->reading_value - ($prev->reading_value ?? 0));
        return ['prev'=>$prev->reading_value ?? 0,'curr'=>$curr->reading_value,'consumption'=>$consumption,'rate'=>$curr->rate_per_unit];
    }

    public function getRevenueChartLabels(int $ownerId): array
    {
        return collect(range(6,0))->map(fn($i) => now()->subMonths($i)->format('M y'))->toArray();
    }

    public function getRevenueChartData(int $ownerId): array
    {
        return collect(range(6,0))->map(fn($i) => (float) TenantBill::where('owner_id',$ownerId)
            ->where('billing_month', now()->subMonths($i)->startOfMonth()->toDateString())->sum('amount_paid'))->toArray();
    }
}
