<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class PropertyDetailController extends Controller {
    public function show(Request $r,$id){
        $p=Property::where('owner_id',$r->user()->id)->withCount(['units','units as occupied_count'=>fn($q)=>$q->where('status','occupied')])->findOrFail($id);
        return response()->json(['success'=>true,'data'=>['id'=>$p->id,'name'=>$p->name,'address'=>$p->address,'city'=>$p->city,'country'=>$p->country,'property_type'=>$p->property_type,'total_floors'=>$p->total_floors,'description'=>$p->description,'status'=>$p->status,'units_count'=>$p->units_count,'occupied_count'=>$p->occupied_count,'vacant_count'=>$p->units_count-$p->occupied_count,'occupancy_rate'=>$p->units_count>0?round($p->occupied_count/$p->units_count*100,1):0,'created_at'=>$p->created_at]]);
    }
    public function update(Request $r,$id){
        $p=Property::where('owner_id',$r->user()->id)->findOrFail($id);
        $data=$r->validate(['name'=>'nullable|string|max:150','address'=>'nullable|string','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','property_type'=>'nullable|in:residential,commercial,mixed','total_floors'=>'nullable|integer|min:1','description'=>'nullable|string','status'=>'nullable|in:active,inactive']);
        if($r->hasFile('image'))$data['image_path']=$r->file('image')->store('property_images','public');
        $p->update($data);
        return response()->json(['success'=>true,'message'=>'Property updated.','data'=>$p]);
    }
    public function destroy(Request $r,$id){
        Property::where('owner_id',$r->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'Property deleted.']);
    }
}
