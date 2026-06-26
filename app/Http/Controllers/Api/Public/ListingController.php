<?php
namespace App\Http\Controllers\Api\Public;
use App\Http\Controllers\Controller;
use App\Models\{Advertisement,Booking};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller {
    public function index(Request $request) {
        $q=Advertisement::public()->with('owner');
        if($city=trim((string)$request->get('city')))$q->where('city','like',"%{$city}%");
        if($beds=$request->get('bedrooms'))$q->where('bedrooms',$beds);
        if($max=$request->get('max_rent'))$q->where('monthly_rent','<=',(float)$max);
        $ads=$q->latest()->paginate(12)->withQueryString();
        return response()->json(['success'=>true,'data'=>$ads->getCollection()->map(fn($a)=>$this->fmt($a)),'meta'=>['total'=>$ads->total(),'current_page'=>$ads->currentPage(),'last_page'=>$ads->lastPage(),'per_page'=>$ads->perPage()]]);
    }
    public function show($id) {
        $ad=Advertisement::where('is_published',true)->whereIn('status',['available','reserved'])->findOrFail($id);
        $ad->increment('views_count');
        return response()->json(['success'=>true,'data'=>$this->fmt($ad,true)]);
    }
    public function book(Request $request,$id) {
        $ad=Advertisement::where('is_published',true)->findOrFail($id);
        $data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:150','phone'=>'nullable|string|max:40','preferred_move_in'=>'nullable|date|after_or_equal:today','message'=>'nullable|string|max:2000']);
        $b=Booking::create(['advertisement_id'=>$ad->id,'owner_id'=>$ad->owner_id,'unit_id'=>$ad->unit_id,'name'=>$data['name'],'email'=>$data['email'],'phone'=>$data['phone']??null,'preferred_move_in'=>$data['preferred_move_in']??null,'message'=>$data['message']??null,'status'=>'new','reference'=>'BK-'.strtoupper(Str::random(8))]);
        return response()->json(['success'=>true,'message'=>'Booking request sent. The owner will contact you directly.','data'=>['reference'=>$b->reference]],201);
    }
    private function fmt(Advertisement $a,bool $full=false):array {
        $base=['id'=>$a->id,'title'=>$a->title,'monthly_rent'=>$a->monthly_rent,'bedrooms'=>$a->bedrooms,'bathrooms'=>$a->bathrooms,'area_sqft'=>$a->area_sqft,'city'=>$a->city,'address'=>$a->address,'status'=>$a->status,'views_count'=>$a->views_count,'image_url'=>$a->image_path?asset('storage/'.$a->image_path):null,'contact_name'=>$a->contact_name,'contact_phone'=>$a->contact_phone,'contact_email'=>$a->contact_email];
        if($full)$base['description']=$a->description;
        return $base;
    }
}
