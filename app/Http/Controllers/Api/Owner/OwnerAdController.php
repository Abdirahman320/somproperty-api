<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class OwnerAdController extends Controller {
    public function index(Request $r){$ads=Advertisement::where('owner_id',$r->user()->id)->with('unit.property')->withCount('bookings')->latest()->get();return response()->json(['success'=>true,'data'=>$ads->map(fn($a)=>$this->fmt($a))]);}
    public function store(Request $r){
        $o=$r->user();
        $data=$r->validate(['unit_id'=>'nullable|exists:units,id','title'=>'required|string|max:180','description'=>'nullable|string','monthly_rent'=>'required|numeric|min:0','bedrooms'=>'nullable|string|max:20','bathrooms'=>'nullable|integer','area_sqft'=>'nullable|numeric','city'=>'nullable|string|max:100','address'=>'nullable|string|max:255','contact_name'=>'required|string|max:120','contact_phone'=>'required|string|max:40','contact_email'=>'nullable|email']);
        $unit=isset($data['unit_id'])?Unit::where('owner_id',$o->id)->find($data['unit_id']):null;
        $img=null;if($r->hasFile('image'))$img=$r->file('image')->store('ad_images','public');
        $ad=Advertisement::create([...$data,'owner_id'=>$o->id,'property_id'=>$unit?->property_id,'image_path'=>$img,'created_by_type'=>'owner','created_by_id'=>$o->id,'is_published'=>true,'status'=>'available']);
        return response()->json(['success'=>true,'data'=>$this->fmt($ad)],201);
    }
    public function update(Request $r,$id){$ad=Advertisement::where('owner_id',$r->user()->id)->findOrFail($id);$ad->update($r->only(['status','is_published','title','description','monthly_rent','contact_name','contact_phone','contact_email']));return response()->json(['success'=>true,'data'=>$this->fmt($ad)]);}
    public function destroy(Request $r,$id){$ad=Advertisement::where('owner_id',$r->user()->id)->findOrFail($id);if($ad->image_path)Storage::disk('public')->delete($ad->image_path);$ad->delete();return response()->json(['success'=>true,'message'=>'Deleted.']);}
    public function bookings(Request $r){$bs=Booking::where('owner_id',$r->user()->id)->with('advertisement')->latest()->paginate(25);return response()->json(['success'=>true,'data'=>$bs->getCollection()->map(fn($b)=>['id'=>$b->id,'reference'=>$b->reference,'name'=>$b->name,'email'=>$b->email,'phone'=>$b->phone,'preferred_move_in'=>$b->preferred_move_in,'message'=>$b->message,'status'=>$b->status,'advertisement'=>$b->advertisement?['id'=>$b->advertisement->id,'title'=>$b->advertisement->title]:null,'created_at'=>$b->created_at]),'meta'=>['total'=>$bs->total(),'current_page'=>$bs->currentPage(),'last_page'=>$bs->lastPage()]]);}
    public function updateBooking(Request $r,$id){$b=Booking::where('owner_id',$r->user()->id)->findOrFail($id);$b->update($r->validate(['status'=>'required|in:new,contacted,viewing_scheduled,closed,cancelled']));return response()->json(['success'=>true,'data'=>$b]);}
    private function fmt(Advertisement $a):array{return['id'=>$a->id,'title'=>$a->title,'description'=>$a->description,'monthly_rent'=>$a->monthly_rent,'bedrooms'=>$a->bedrooms,'bathrooms'=>$a->bathrooms,'area_sqft'=>$a->area_sqft,'city'=>$a->city,'address'=>$a->address,'status'=>$a->status,'is_published'=>$a->is_published,'views_count'=>$a->views_count,'contact_name'=>$a->contact_name,'contact_phone'=>$a->contact_phone,'contact_email'=>$a->contact_email,'image_url'=>$a->image_path?asset('storage/'.$a->image_path):null,'bookings_count'=>$a->bookings_count??0,'unit'=>$a->unit?['id'=>$a->unit->id,'unit_number'=>$a->unit->unit_number]:null,'created_at'=>$a->created_at];}
}
