<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class TenantDetailController extends Controller {
    public function show(Request $r,$id){$t=Tenant::where('owner_id',$r->user()->id)->with(['activeContract.unit.property','documents'])->findOrFail($id);return response()->json(['success'=>true,'data'=>$this->fmt($t)]);}
    public function update(Request $r,$id){$t=Tenant::where('owner_id',$r->user()->id)->findOrFail($id);$t->update($r->validate(['full_name'=>'nullable|string|max:100','phone'=>'nullable|string|max:30','national_id'=>'nullable|string|max:50','emergency_contact'=>'nullable|string|max:100','emergency_phone'=>'nullable|string|max:30','date_of_birth'=>'nullable|date','notes'=>'nullable|string|max:2000','status'=>'nullable|in:active,inactive,blacklisted']));return response()->json(['success'=>true,'message'=>'Tenant updated.','data'=>$this->fmt($t->fresh())]);}
    public function destroy(Request $r,$id){Tenant::where('owner_id',$r->user()->id)->findOrFail($id)->delete();return response()->json(['success'=>true,'message'=>'Tenant deleted.']);}
    public function contracts(Request $r,$id){$cs=Contract::where('owner_id',$r->user()->id)->where('tenant_id',$id)->with('unit.property')->latest()->get()->map(fn($c)=>$this->fmtC($c));return response()->json(['success'=>true,'data'=>$cs]);}
    public function storeContract(Request $r,$id){
        $o=$r->user();Tenant::where('owner_id',$o->id)->findOrFail($id);
        $data=$r->validate(['unit_id'=>'required|exists:units,id','start_date'=>'required|date','end_date'=>'required|date|after:start_date','monthly_rent'=>'required|numeric|min:0','security_deposit'=>'nullable|numeric|min:0','payment_due_day'=>'nullable|integer|min:1|max:28']);
        $c=Contract::create(['owner_id'=>$o->id,'tenant_id'=>$id,'status'=>'active',...$data]);
        Unit::find($data['unit_id'])->update(['status'=>'occupied']);
        return response()->json(['success'=>true,'data'=>$this->fmtC($c->load('unit.property'))],201);
    }
    public function terminateContract(Request $r,$cid){$c=Contract::where('owner_id',$r->user()->id)->findOrFail($cid);$c->update(['status'=>'terminated','terminated_at'=>now(),'termination_reason'=>$r->get('reason')]);$c->unit?->update(['status'=>'vacant']);return response()->json(['success'=>true,'message'=>'Contract terminated.']);}
    public function renewContract(Request $r,$cid){
        $o=$r->user();$old=Contract::where('owner_id',$o->id)->findOrFail($cid);
        $data=$r->validate(['start_date'=>'required|date','end_date'=>'required|date|after:start_date','monthly_rent'=>'required|numeric|min:0','security_deposit'=>'nullable|numeric|min:0']);
        $old->update(['status'=>'expired']);
        $new=Contract::create(['owner_id'=>$o->id,'tenant_id'=>$old->tenant_id,'unit_id'=>$old->unit_id,'status'=>'active','payment_due_day'=>$old->payment_due_day??1,'grace_period_days'=>$old->grace_period_days??5,'late_fee_amount'=>$old->late_fee_amount??0,'renewed_from_id'=>$old->id,...$data]);
        $new->unit?->update(['status'=>'occupied']);
        return response()->json(['success'=>true,'message'=>'Contract renewed.','data'=>$this->fmtC($new->load('unit.property'))]);
    }
    private function fmt(Tenant $t):array{return['id'=>$t->id,'full_name'=>$t->full_name,'email'=>$t->email,'phone'=>$t->phone,'national_id'=>$t->national_id,'emergency_contact'=>$t->emergency_contact??null,'emergency_phone'=>$t->emergency_phone??null,'date_of_birth'=>$t->date_of_birth??null,'notes'=>$t->notes,'status'=>$t->status,'documents_count'=>$t->documents?->count()??0,'contract'=>$t->activeContract?$this->fmtC($t->activeContract):null,'created_at'=>$t->created_at];}
    private function fmtC(Contract $c):array{return['id'=>$c->id,'start_date'=>$c->start_date,'end_date'=>$c->end_date,'monthly_rent'=>$c->monthly_rent,'security_deposit'=>$c->security_deposit,'payment_due_day'=>$c->payment_due_day,'grace_period_days'=>$c->grace_period_days,'late_fee_amount'=>$c->late_fee_amount,'status'=>$c->status,'renewed_from_id'=>$c->renewed_from_id,'terminated_at'=>$c->terminated_at??null,'unit'=>$c->unit?['id'=>$c->unit->id,'unit_number'=>$c->unit->unit_number,'property_name'=>$c->unit->property?->name]:null];}
}
