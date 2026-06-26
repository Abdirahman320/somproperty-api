<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class UnitController2 extends Controller {
    public function show(Request $r,$id){
        $u=Unit::where('owner_id',$r->user()->id)->with(['property','activeContract.tenant'])->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$this->fmt($u)]);
    }
    public function store(Request $r){
        $data=$r->validate(['property_id'=>'required|exists:properties,id','unit_number'=>'required|string|max:20','floor_number'=>'nullable|integer','bedrooms'=>'nullable|string|max:20','bathrooms'=>'nullable|integer','area_sqft'=>'nullable|numeric|min:0','monthly_rent'=>'required|numeric|min:0','status'=>'nullable|in:vacant,occupied,maintenance,reserved']);
        $u=Unit::create(['owner_id'=>$r->user()->id,...$data]);
        return response()->json(['success'=>true,'data'=>$this->fmt($u)],201);
    }
    public function update(Request $r,$id){
        $u=Unit::where('owner_id',$r->user()->id)->findOrFail($id);
        $u->update($r->validate(['unit_number'=>'nullable|string|max:20','floor_number'=>'nullable|integer','bedrooms'=>'nullable|string|max:20','bathrooms'=>'nullable|integer','area_sqft'=>'nullable|numeric|min:0','monthly_rent'=>'nullable|numeric|min:0','status'=>'nullable|in:vacant,occupied,maintenance,reserved']));
        return response()->json(['success'=>true,'data'=>$this->fmt($u)]);
    }
    public function destroy(Request $r,$id){
        Unit::where('owner_id',$r->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'Unit deleted.']);
    }
    private function fmt(Unit $u):array{
        return['id'=>$u->id,'unit_number'=>$u->unit_number,'floor_number'=>$u->floor_number,'bedrooms'=>$u->bedrooms,'bathrooms'=>$u->bathrooms,'area_sqft'=>$u->area_sqft,'monthly_rent'=>$u->monthly_rent,'status'=>$u->status,'property_id'=>$u->property_id,'property'=>$u->property?['id'=>$u->property->id,'name'=>$u->property->name]:null,'tenant'=>$u->activeContract?->tenant?['id'=>$u->activeContract->tenant->id,'full_name'=>$u->activeContract->tenant->full_name]:null];
    }
}
