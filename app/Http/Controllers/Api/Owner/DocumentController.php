<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class DocumentController extends Controller {
    public function index(Request $r){
        $o=$r->user();$q=TenantDocument::where('owner_id',$o->id)->with('tenant');
        if($type=$r->get('doc_type'))$q->where('doc_type',$type);
        if($f=$r->get('filter')){if($f==='expiring')$q->whereNotNull('expires_on')->whereDate('expires_on','>=',now())->whereDate('expires_on','<=',now()->addDays(30));if($f==='expired')$q->whereNotNull('expires_on')->whereDate('expires_on','<',now());}
        $docs=$q->latest()->paginate(30);$b=TenantDocument::where('owner_id',$o->id);
        return response()->json(['success'=>true,'data'=>$docs->getCollection()->map(fn($d)=>$this->fmtDoc($d)),'stats'=>['total'=>(clone $b)->count(),'expired'=>(clone $b)->whereNotNull('expires_on')->whereDate('expires_on','<',now())->count(),'expiring'=>(clone $b)->whereNotNull('expires_on')->whereDate('expires_on','>=',now())->whereDate('expires_on','<=',now()->addDays(30))->count()],'meta'=>['total'=>$docs->total(),'current_page'=>$docs->currentPage(),'last_page'=>$docs->lastPage()]]);
    }
    public function tenantDocs(Request $r,$tid){$docs=TenantDocument::where('owner_id',$r->user()->id)->where('tenant_id',$tid)->with('tenant')->latest()->get();return response()->json(['success'=>true,'data'=>$docs->map(fn($d)=>$this->fmtDoc($d))]);}
    public function store(Request $r,$tid){
        $o=$r->user();Tenant::where('owner_id',$o->id)->findOrFail($tid);
        $data=$r->validate(['doc_type'=>'required|in:passport,police_certificate,national_id,visa,residence_permit,employment_letter,bank_statement,other','label'=>'nullable|string|max:150','issued_on'=>'nullable|date','expires_on'=>'nullable|date','notes'=>'nullable|string|max:1000','files'=>'required|array|min:1|max:20','files.*'=>'file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240']);
        $created=[];foreach($r->file('files') as $file){$path=$file->store("tenant_documents/{$tid}");$created[]=TenantDocument::create(['owner_id'=>$o->id,'tenant_id'=>$tid,'doc_type'=>$data['doc_type'],'label'=>$data['label']??null,'file_path'=>$path,'original_name'=>$file->getClientOriginalName(),'mime_type'=>$file->getClientMimeType(),'size_bytes'=>$file->getSize(),'issued_on'=>$data['issued_on']??null,'expires_on'=>$data['expires_on']??null,'uploaded_by'=>'owner','uploaded_by_id'=>$o->id,'notes'=>$data['notes']??null]);}
        $n=count($created);
        return response()->json(['success'=>true,'message'=>$n===1?'Document uploaded.':"{$n} documents uploaded.",'data'=>array_map(fn($d)=>$this->fmtDoc($d),$created)],201);
    }
    public function download(Request $r,$id){$doc=TenantDocument::where('owner_id',$r->user()->id)->findOrFail($id);abort_unless(Storage::exists($doc->file_path),404);return Storage::download($doc->file_path,$doc->original_name??basename($doc->file_path));}
    public function destroy(Request $r,$id){$doc=TenantDocument::where('owner_id',$r->user()->id)->findOrFail($id);Storage::delete($doc->file_path);$doc->delete();return response()->json(['success'=>true,'message'=>'Document deleted.']);}
    private function fmtDoc(TenantDocument $d):array{$eb=$d->expiryBadge();return['id'=>$d->id,'tenant_id'=>$d->tenant_id,'tenant_name'=>$d->tenant?->full_name,'doc_type'=>$d->doc_type,'type_label'=>$d->typeLabel(),'label'=>$d->label,'original_name'=>$d->original_name,'mime_type'=>$d->mime_type,'size_bytes'=>$d->size_bytes,'issued_on'=>$d->issued_on,'expires_on'=>$d->expires_on,'notes'=>$d->notes,'uploaded_by'=>$d->uploaded_by,'expiry_status'=>$eb?$eb['class']:'none','expiry_text'=>$eb?$eb['text']:null,'is_expired'=>$d->isExpired(),'is_expiring_soon'=>$d->isExpiringSoon(),'created_at'=>$d->created_at];}
}
