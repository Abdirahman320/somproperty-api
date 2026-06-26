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
        if($r->hasFile('image')) $data['image_path']=$r->file('image')->store('property_images','public');
        $p->update($data);
        return response()->json(['success'=>true,'message'=>'Property updated.','data'=>$p]);
    }
    public function destroy(Request $r,$id){
        Property::where('owner_id',$r->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'Property deleted.']);
    }
}

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
        $data=$r->validate(['unit_number'=>'nullable|string|max:20','floor_number'=>'nullable|integer','bedrooms'=>'nullable|string|max:20','bathrooms'=>'nullable|integer','area_sqft'=>'nullable|numeric|min:0','monthly_rent'=>'nullable|numeric|min:0','status'=>'nullable|in:vacant,occupied,maintenance,reserved']);
        $u->update($data);
        return response()->json(['success'=>true,'data'=>$this->fmt($u)]);
    }
    public function destroy(Request $r,$id){
        Unit::where('owner_id',$r->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'Unit deleted.']);
    }
    private function fmt(Unit $u):array{
        return ['id'=>$u->id,'unit_number'=>$u->unit_number,'floor_number'=>$u->floor_number,'bedrooms'=>$u->bedrooms,'bathrooms'=>$u->bathrooms,'area_sqft'=>$u->area_sqft,'monthly_rent'=>$u->monthly_rent,'status'=>$u->status,'property_id'=>$u->property_id,'property'=>$u->property?['id'=>$u->property->id,'name'=>$u->property->name]:null,'tenant'=>$u->activeContract?->tenant?['id'=>$u->activeContract->tenant->id,'full_name'=>$u->activeContract->tenant->full_name]:null];
    }
}

class TenantDetailController extends Controller {
    public function show(Request $r,$id){
        $t=Tenant::where('owner_id',$r->user()->id)->with(['activeContract.unit.property','documents'])->findOrFail($id);
        return response()->json(['success'=>true,'data'=>$this->fmt($t)]);
    }
    public function update(Request $r,$id){
        $t=Tenant::where('owner_id',$r->user()->id)->findOrFail($id);
        $data=$r->validate(['full_name'=>'nullable|string|max:100','phone'=>'nullable|string|max:30','national_id'=>'nullable|string|max:50','emergency_contact'=>'nullable|string|max:100','emergency_phone'=>'nullable|string|max:30','date_of_birth'=>'nullable|date','notes'=>'nullable|string|max:2000','status'=>'nullable|in:active,inactive,blacklisted']);
        $t->update($data);
        return response()->json(['success'=>true,'message'=>'Tenant updated.','data'=>$this->fmt($t->fresh())]);
    }
    public function destroy(Request $r,$id){
        Tenant::where('owner_id',$r->user()->id)->findOrFail($id)->delete();
        return response()->json(['success'=>true,'message'=>'Tenant deleted.']);
    }
    public function contracts(Request $r,$id){
        $cs=Contract::where('owner_id',$r->user()->id)->where('tenant_id',$id)->with('unit.property')->latest()->get()->map(fn($c)=>$this->fmtC($c));
        return response()->json(['success'=>true,'data'=>$cs]);
    }
    public function storeContract(Request $r,$id){
        $o=$r->user(); Tenant::where('owner_id',$o->id)->findOrFail($id);
        $data=$r->validate(['unit_id'=>'required|exists:units,id','start_date'=>'required|date','end_date'=>'required|date|after:start_date','monthly_rent'=>'required|numeric|min:0','security_deposit'=>'nullable|numeric|min:0','payment_due_day'=>'nullable|integer|min:1|max:28','grace_period_days'=>'nullable|integer|min:0','late_fee_amount'=>'nullable|numeric|min:0']);
        $c=Contract::create(['owner_id'=>$o->id,'tenant_id'=>$id,'status'=>'active',...$data]);
        Unit::find($data['unit_id'])->update(['status'=>'occupied']);
        return response()->json(['success'=>true,'data'=>$this->fmtC($c->load('unit.property'))],201);
    }
    public function terminateContract(Request $r,$contractId){
        $c=Contract::where('owner_id',$r->user()->id)->findOrFail($contractId);
        $c->update(['status'=>'terminated','terminated_at'=>now(),'termination_reason'=>$r->get('reason')]);
        $c->unit?->update(['status'=>'vacant']);
        return response()->json(['success'=>true,'message'=>'Contract terminated.']);
    }
    public function renewContract(Request $r,$contractId){
        $o=$r->user(); $old=Contract::where('owner_id',$o->id)->findOrFail($contractId);
        $data=$r->validate(['start_date'=>'required|date','end_date'=>'required|date|after:start_date','monthly_rent'=>'required|numeric|min:0','security_deposit'=>'nullable|numeric|min:0']);
        $old->update(['status'=>'expired']);
        $new=Contract::create(['owner_id'=>$o->id,'tenant_id'=>$old->tenant_id,'unit_id'=>$old->unit_id,'status'=>'active','payment_due_day'=>$old->payment_due_day??1,'grace_period_days'=>$old->grace_period_days??5,'late_fee_amount'=>$old->late_fee_amount??0,'renewed_from_id'=>$old->id,...$data]);
        $new->unit?->update(['status'=>'occupied']);
        return response()->json(['success'=>true,'message'=>'Contract renewed.','data'=>$this->fmtC($new->load('unit.property'))]);
    }
    private function fmt(Tenant $t):array{return['id'=>$t->id,'full_name'=>$t->full_name,'email'=>$t->email,'phone'=>$t->phone,'national_id'=>$t->national_id,'emergency_contact'=>$t->emergency_contact,'emergency_phone'=>$t->emergency_phone,'date_of_birth'=>$t->date_of_birth,'notes'=>$t->notes,'status'=>$t->status,'documents_count'=>$t->documents?->count()??0,'contract'=>$t->activeContract?$this->fmtC($t->activeContract):null,'created_at'=>$t->created_at];}
    private function fmtC(Contract $c):array{return['id'=>$c->id,'start_date'=>$c->start_date,'end_date'=>$c->end_date,'monthly_rent'=>$c->monthly_rent,'security_deposit'=>$c->security_deposit,'payment_due_day'=>$c->payment_due_day,'grace_period_days'=>$c->grace_period_days,'late_fee_amount'=>$c->late_fee_amount,'status'=>$c->status,'renewed_from_id'=>$c->renewed_from_id,'terminated_at'=>$c->terminated_at,'unit'=>$c->unit?['id'=>$c->unit->id,'unit_number'=>$c->unit->unit_number,'property_name'=>$c->unit->property?->name]:null];}
}

class DocumentController extends Controller {
    public function index(Request $r){
        $o=$r->user(); $q=TenantDocument::where('owner_id',$o->id)->with('tenant');
        if($type=$r->get('doc_type')) $q->where('doc_type',$type);
        if($f=$r->get('filter')){if($f==='expiring')$q->whereNotNull('expires_on')->whereDate('expires_on','>=',now())->whereDate('expires_on','<=',now()->addDays(30));if($f==='expired')$q->whereNotNull('expires_on')->whereDate('expires_on','<',now());}
        $docs=$q->latest()->paginate(30);
        $b=TenantDocument::where('owner_id',$o->id);
        return response()->json(['success'=>true,'data'=>$docs->getCollection()->map(fn($d)=>$this->fmtDoc($d)),'stats'=>['total'=>(clone $b)->count(),'expired'=>(clone $b)->whereNotNull('expires_on')->whereDate('expires_on','<',now())->count(),'expiring'=>(clone $b)->whereNotNull('expires_on')->whereDate('expires_on','>=',now())->whereDate('expires_on','<=',now()->addDays(30))->count()],'meta'=>['total'=>$docs->total(),'current_page'=>$docs->currentPage(),'last_page'=>$docs->lastPage()]]);
    }
    public function tenantDocs(Request $r,$tenantId){
        $docs=TenantDocument::where('owner_id',$r->user()->id)->where('tenant_id',$tenantId)->with('tenant')->latest()->get();
        return response()->json(['success'=>true,'data'=>$docs->map(fn($d)=>$this->fmtDoc($d))]);
    }
    public function store(Request $r,$tenantId){
        $o=$r->user(); Tenant::where('owner_id',$o->id)->findOrFail($tenantId);
        $data=$r->validate(['doc_type'=>'required|in:passport,police_certificate,national_id,visa,residence_permit,employment_letter,bank_statement,other','label'=>'nullable|string|max:150','issued_on'=>'nullable|date','expires_on'=>'nullable|date','notes'=>'nullable|string|max:1000','files'=>'required|array|min:1|max:20','files.*'=>'file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240']);
        $created=[];
        foreach($r->file('files') as $file){
            $path=$file->store("tenant_documents/{$tenantId}");
            $created[]=TenantDocument::create(['owner_id'=>$o->id,'tenant_id'=>$tenantId,'doc_type'=>$data['doc_type'],'label'=>$data['label']??null,'file_path'=>$path,'original_name'=>$file->getClientOriginalName(),'mime_type'=>$file->getClientMimeType(),'size_bytes'=>$file->getSize(),'issued_on'=>$data['issued_on']??null,'expires_on'=>$data['expires_on']??null,'uploaded_by'=>'owner','uploaded_by_id'=>$o->id,'notes'=>$data['notes']??null]);
        }
        $n=count($created);
        return response()->json(['success'=>true,'message'=>$n===1?'Document uploaded.':"{$n} documents uploaded.",'data'=>array_map(fn($d)=>$this->fmtDoc($d),$created)],201);
    }
    public function download(Request $r,$id){
        $doc=TenantDocument::where('owner_id',$r->user()->id)->findOrFail($id);
        abort_unless(Storage::exists($doc->file_path),404);
        return Storage::download($doc->file_path,$doc->original_name??basename($doc->file_path));
    }
    public function destroy(Request $r,$id){
        $doc=TenantDocument::where('owner_id',$r->user()->id)->findOrFail($id);
        Storage::delete($doc->file_path); $doc->delete();
        return response()->json(['success'=>true,'message'=>'Document deleted.']);
    }
    private function fmtDoc(TenantDocument $d):array{$eb=$d->expiryBadge();return['id'=>$d->id,'tenant_id'=>$d->tenant_id,'tenant_name'=>$d->tenant?->full_name,'doc_type'=>$d->doc_type,'type_label'=>$d->typeLabel(),'label'=>$d->label,'original_name'=>$d->original_name,'mime_type'=>$d->mime_type,'size_bytes'=>$d->size_bytes,'issued_on'=>$d->issued_on,'expires_on'=>$d->expires_on,'notes'=>$d->notes,'uploaded_by'=>$d->uploaded_by,'expiry_status'=>$eb?$eb['class']:'none','expiry_text'=>$eb?$eb['text']:null,'is_expired'=>$d->isExpired(),'is_expiring_soon'=>$d->isExpiringSoon(),'created_at'=>$d->created_at];}
}

class OwnerAdController extends Controller {
    public function index(Request $r){$ads=Advertisement::where('owner_id',$r->user()->id)->with('unit.property')->withCount('bookings')->latest()->get();return response()->json(['success'=>true,'data'=>$ads->map(fn($a)=>$this->fmt($a))]);}
    public function store(Request $r){
        $o=$r->user();
        $data=$r->validate(['unit_id'=>'nullable|exists:units,id','title'=>'required|string|max:180','description'=>'nullable|string','monthly_rent'=>'required|numeric|min:0','bedrooms'=>'nullable|string|max:20','bathrooms'=>'nullable|integer','area_sqft'=>'nullable|numeric','city'=>'nullable|string|max:100','address'=>'nullable|string|max:255','contact_name'=>'required|string|max:120','contact_phone'=>'required|string|max:40','contact_email'=>'nullable|email']);
        $unit=isset($data['unit_id'])?Unit::where('owner_id',$o->id)->find($data['unit_id']):null;
        $img=null; if($r->hasFile('image'))$img=$r->file('image')->store('ad_images','public');
        $ad=Advertisement::create([...$data,'owner_id'=>$o->id,'property_id'=>$unit?->property_id,'image_path'=>$img,'created_by_type'=>'owner','created_by_id'=>$o->id,'is_published'=>true,'status'=>'available']);
        return response()->json(['success'=>true,'data'=>$this->fmt($ad)],201);
    }
    public function update(Request $r,$id){$ad=Advertisement::where('owner_id',$r->user()->id)->findOrFail($id);$ad->update($r->only(['status','is_published','title','description','monthly_rent','contact_name','contact_phone','contact_email']));return response()->json(['success'=>true,'data'=>$this->fmt($ad)]);}
    public function destroy(Request $r,$id){$ad=Advertisement::where('owner_id',$r->user()->id)->findOrFail($id);if($ad->image_path)Storage::disk('public')->delete($ad->image_path);$ad->delete();return response()->json(['success'=>true,'message'=>'Deleted.']);}
    public function bookings(Request $r){$bs=Booking::where('owner_id',$r->user()->id)->with('advertisement')->latest()->paginate(25);return response()->json(['success'=>true,'data'=>$bs->getCollection()->map(fn($b)=>['id'=>$b->id,'reference'=>$b->reference,'name'=>$b->name,'email'=>$b->email,'phone'=>$b->phone,'preferred_move_in'=>$b->preferred_move_in,'message'=>$b->message,'status'=>$b->status,'advertisement'=>$b->advertisement?['id'=>$b->advertisement->id,'title'=>$b->advertisement->title]:null,'created_at'=>$b->created_at]),'meta'=>['total'=>$bs->total(),'current_page'=>$bs->currentPage(),'last_page'=>$bs->lastPage()]]);}
    public function updateBooking(Request $r,$id){$b=Booking::where('owner_id',$r->user()->id)->findOrFail($id);$b->update($r->validate(['status'=>'required|in:new,contacted,viewing_scheduled,closed,cancelled']));return response()->json(['success'=>true,'data'=>$b]);}
    private function fmt(Advertisement $a):array{return['id'=>$a->id,'title'=>$a->title,'description'=>$a->description,'monthly_rent'=>$a->monthly_rent,'bedrooms'=>$a->bedrooms,'bathrooms'=>$a->bathrooms,'area_sqft'=>$a->area_sqft,'city'=>$a->city,'address'=>$a->address,'status'=>$a->status,'is_published'=>$a->is_published,'views_count'=>$a->views_count,'contact_name'=>$a->contact_name,'contact_phone'=>$a->contact_phone,'contact_email'=>$a->contact_email,'image_url'=>$a->image_path?asset('storage/'.$a->image_path):null,'bookings_count'=>$a->bookings_count??0,'unit'=>$a->unit?['id'=>$a->unit->id,'unit_number'=>$a->unit->unit_number]:null,'created_at'=>$a->created_at];}
}

class UtilityController extends Controller {
    public function index(Request $r){
        $q=UtilityReading::where('owner_id',$r->user()->id)->with('unit');
        if($uid=$r->get('unit_id'))$q->where('unit_id',$uid);
        $rs=$q->latest('reading_date')->paginate(30);
        return response()->json(['success'=>true,'data'=>$rs->getCollection()->map(fn($r2)=>['id'=>$r2->id,'unit_id'=>$r2->unit_id,'unit_number'=>$r2->unit?->unit_number,'utility_type'=>$r2->utility_type,'reading_date'=>$r2->reading_date,'reading_value'=>$r2->reading_value,'rate_per_unit'=>$r2->rate_per_unit,'created_at'=>$r2->created_at]),'meta'=>['total'=>$rs->total(),'current_page'=>$rs->currentPage(),'last_page'=>$rs->lastPage()]]);
    }
}

class AssetController2 extends Controller {
    public function issues(Request $r){$is=TechnicalIssue::where('owner_id',$r->user()->id)->with('asset')->latest()->paginate(25);return response()->json(['success'=>true,'data'=>$is->getCollection()->map(fn($i)=>['id'=>$i->id,'asset_id'=>$i->asset_id,'asset_name'=>$i->asset?->asset_name,'issue_title'=>$i->issue_title,'description'=>$i->description,'priority'=>$i->priority,'status'=>$i->status,'estimated_cost'=>$i->estimated_cost,'actual_cost'=>$i->actual_cost,'reported_at'=>$i->reported_at,'resolved_at'=>$i->resolved_at]),'meta'=>['total'=>$is->total(),'current_page'=>$is->currentPage(),'last_page'=>$is->lastPage()]]);}
    public function updateIssue(Request $r,$id){$i=TechnicalIssue::where('owner_id',$r->user()->id)->findOrFail($id);$i->update($r->validate(['status'=>'nullable|in:reported,in_progress,resolved,closed','actual_cost'=>'nullable|numeric|min:0','notes'=>'nullable|string|max:1000','resolved_at'=>'nullable|date']));return response()->json(['success'=>true,'message'=>'Issue updated.','data'=>$i]);}
}

class OwnerBackupController extends Controller {
    public function __construct(private DataBackupService $backup){}
    public function export(Request $r){
        $oid=$r->user()->id;$format=$r->validate(['format'=>'required|in:excel,csv,sql'])['format'];$stamp=now()->format('Ymd_His');
        [$content,$filename,$mime]=match($format){'csv'=>[$this->backup->exportCsv('owner',$oid),"som_owner{$oid}_{$stamp}.csv",'text/csv'],'sql'=>[$this->backup->exportSql('owner',$oid),"som_owner{$oid}_{$stamp}.sql",'application/sql'],default=>[$this->backup->exportExcel('owner',$oid),"som_owner{$oid}_{$stamp}.xls",'application/vnd.ms-excel']};
        return response($content,200,['Content-Type'=>$mime,'Content-Disposition'=>'attachment; filename="'.$filename.'"']);
    }
    public function import(Request $r){
        $r->validate(['file'=>'required|file|extensions:csv,txt,sql,xls,xml|max:51200']);
        $file=$r->file('file');
        try{$report=$this->backup->import($file->getRealPath(),strtolower($file->getClientOriginalExtension()),'owner',$r->user()->id);}catch(\Throwable $e){return response()->json(['success'=>false,'message'=>'Restore failed: '.$e->getMessage()],422);}
        $s=$report['tables']===null?"{$report['rows']} SQL statements executed.":"{$report['rows']} rows imported across {$report['tables']} table(s).";
        return response()->json(['success'=>true,'message'=>$s,'data'=>$report]);
    }
}

class OwnerSettingsController extends Controller {
    public function index(Request $r){$o=$r->user();return response()->json(['success'=>true,'data'=>['id'=>$o->id,'full_name'=>$o->full_name,'company_name'=>$o->company_name,'email'=>$o->email,'phone'=>$o->phone,'max_apartments'=>$o->max_apartments,'plan'=>$o->plan?['name'=>$o->plan->name,'price_monthly'=>$o->plan->price_monthly]:null,'subscription_ends_at'=>$o->subscription_ends_at]]);}
    public function update(Request $r){
        $o=$r->user();$data=$r->validate(['full_name'=>'nullable|string|max:100','company_name'=>'nullable|string|max:150','phone'=>'nullable|string|max:30','password'=>'nullable|string|min:8|confirmed']);
        if(!empty($data['password'])){$data['password_hash']=Hash::make($data['password']);unset($data['password'],$data['password_confirmation']);}
        $o->update($data);return response()->json(['success'=>true,'message'=>'Settings saved.']);
    }
}
