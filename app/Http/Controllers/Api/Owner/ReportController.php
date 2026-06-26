<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{TenantBill,Contract,Unit};
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller {
    public function show(Request $r, string $type) {
        $o=$r->user();
        return match($type) {
            'occupancy' => $this->occupancy($o),
            'revenue'   => $this->revenue($o),
            'outstanding' => $this->outstanding($o),
            'tenants'   => $this->tenants($o),
            default     => response()->json(['success'=>false,'message'=>"Report '{$type}' not found."],404),
        };
    }
    private function occupancy($o) {
        $units=Unit::where('owner_id',$o->id)->with('property')->get();
        $data=['total'=>$units->count(),'occupied'=>$units->where('status','occupied')->count(),'vacant'=>$units->where('status','vacant')->count(),'maintenance'=>$units->where('status','maintenance')->count(),'occupancy_rate'=>$units->count()>0?round($units->where('status','occupied')->count()/$units->count()*100,1):0,'by_property'=>$units->groupBy('property_id')->map(fn($pu)=>['property'=>$pu->first()?->property?->name,'total'=>$pu->count(),'occupied'=>$pu->where('status','occupied')->count()])];
        return response()->json(['success'=>true,'type'=>'occupancy','data'=>$data]);
    }
    private function revenue($o) {
        $rows=[];for($i=11;$i>=0;$i--){$m=Carbon::now()->subMonths($i)->startOfMonth();$rows[]=['month'=>$m->format('M Y'),'collected'=>(float)TenantBill::where('owner_id',$o->id)->whereDate('billing_month',$m->toDateString())->sum('amount_paid'),'billed'=>(float)TenantBill::where('owner_id',$o->id)->whereDate('billing_month',$m->toDateString())->sum('total_amount')];}
        return response()->json(['success'=>true,'type'=>'revenue','data'=>$rows]);
    }
    private function outstanding($o) {
        $bills=TenantBill::where('owner_id',$o->id)->whereIn('status',['pending','overdue','partially_paid'])->with(['tenant','unit'])->get()->map(fn($b)=>['tenant'=>$b->tenant?->full_name,'unit'=>$b->unit?->unit_number,'month'=>$b->billing_month->format('M Y'),'balance_due'=>(float)$b->balance_due,'status'=>$b->status,'due_date'=>$b->due_date]);
        return response()->json(['success'=>true,'type'=>'outstanding','data'=>$bills,'total_outstanding'=>$bills->sum('balance_due')]);
    }
    private function tenants($o) {
        $tenants=\App\Models\Tenant::where('owner_id',$o->id)->with(['activeContract.unit.property'])->get()->map(fn($t)=>['id'=>$t->id,'full_name'=>$t->full_name,'email'=>$t->email,'phone'=>$t->phone,'status'=>$t->status,'unit'=>$t->activeContract?->unit?->unit_number,'property'=>$t->activeContract?->unit?->property?->name,'rent'=>$t->activeContract?->monthly_rent,'contract_end'=>$t->activeContract?->end_date]);
        return response()->json(['success'=>true,'type'=>'tenants','data'=>$tenants]);
    }
}
