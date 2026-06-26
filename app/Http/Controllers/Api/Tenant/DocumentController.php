<?php
namespace App\Http\Controllers\Api\Tenant;
use App\Http\Controllers\Controller;
use App\Models\TenantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller {
    public function index(Request $r) {
        $docs=TenantDocument::where('tenant_id',$r->user()->id)->latest()->get();
        return response()->json(['success'=>true,'data'=>$docs->map(fn($d)=>$this->fmt($d))]);
    }
    public function download(Request $r,$id) {
        $doc=TenantDocument::where('tenant_id',$r->user()->id)->findOrFail($id);
        abort_unless(Storage::exists($doc->file_path),404);
        return Storage::download($doc->file_path,$doc->original_name??basename($doc->file_path));
    }
    private function fmt(TenantDocument $d):array {
        $eb=$d->expiryBadge();
        return['id'=>$d->id,'doc_type'=>$d->doc_type,'type_label'=>$d->typeLabel(),'label'=>$d->label,'original_name'=>$d->original_name,'size_bytes'=>$d->size_bytes,'issued_on'=>$d->issued_on,'expires_on'=>$d->expires_on,'notes'=>$d->notes,'expiry_status'=>$eb?$eb['class']:'none','expiry_text'=>$eb?$eb['text']:null,'is_expired'=>$d->isExpired(),'uploaded_by'=>$d->uploaded_by,'created_at'=>$d->created_at];
    }
}
