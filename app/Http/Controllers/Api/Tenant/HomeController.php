<?php
namespace App\Http\Controllers\Api\Tenant;
use App\Http\Controllers\Controller;
use App\Models\{TenantBill, TenantNotification};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tenant   = $request->user();
        $contract = $tenant->activeContract?->load('unit.property');
        $unread   = TenantNotification::where('tenant_id',$tenant->id)->where('is_read',false)->count();
        $latestBill = TenantBill::where('tenant_id',$tenant->id)->latest('billing_month')->first();

        return response()->json([
            'user' => ['id'=>$tenant->id,'full_name'=>$tenant->full_name,'email'=>$tenant->email,'phone'=>$tenant->phone,'role'=>'tenant'],
            'tenant' => ['id'=>$tenant->id,'full_name'=>$tenant->full_name,'email'=>$tenant->email,'phone'=>$tenant->phone],
            'contract' => $contract ? [
                'id'              => $contract->id,
                'start_date'      => $contract->start_date,
                'end_date'        => $contract->end_date,
                'monthly_rent'    => (float)$contract->monthly_rent,
                'security_deposit'=> (float)$contract->security_deposit,
                'status'          => $contract->status,
                'unit' => [
                    'id'          => $contract->unit->id,
                    'unit_number' => $contract->unit->unit_number,
                    'floor_number'=> $contract->unit->floor_number,
                    'bedrooms'    => $contract->unit->bedrooms,
                    'area_sqft'   => $contract->unit->area_sqft,
                    'property'    => [
                        'name'    => $contract->unit->property->name,
                        'address' => $contract->unit->property->address,
                        'city'    => $contract->unit->property->city,
                    ],
                ],
            ] : null,
            'latest_bill' => $latestBill ? [
                'id'           => $latestBill->id,
                'billing_month'=> $latestBill->billing_month->format('Y-m-d'),
                'due_date'     => $latestBill->due_date->format('Y-m-d'),
                'total_amount' => (float)$latestBill->total_amount,
                'amount_paid'  => (float)$latestBill->amount_paid,
                'balance_due'  => (float)$latestBill->balance_due,
                'status'       => $latestBill->status,
            ] : null,
            'unread_notifications' => $unread,
        ]);
    }
}
