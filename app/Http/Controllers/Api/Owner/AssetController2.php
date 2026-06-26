<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class AssetController2 extends Controller {
    public function issues(Request $r){$is=TechnicalIssue::where('owner_id',$r->user()->id)->with('asset')->latest()->paginate(25);return response()->json(['success'=>true,'data'=>$is->getCollection()->map(fn($i)=>['id'=>$i->id,'asset_id'=>$i->asset_id,'asset_name'=>$i->asset?->asset_name,'issue_title'=>$i->issue_title,'description'=>$i->description,'priority'=>$i->priority,'status'=>$i->status,'estimated_cost'=>$i->estimated_cost,'actual_cost'=>$i->actual_cost,'reported_at'=>$i->reported_at,'resolved_at'=>$i->resolved_at]),'meta'=>['total'=>$is->total(),'current_page'=>$is->currentPage(),'last_page'=>$is->lastPage()]]);}
    public function updateIssue(Request $r,$id){$i=TechnicalIssue::where('owner_id',$r->user()->id)->findOrFail($id);$i->update($r->validate(['status'=>'nullable|in:reported,in_progress,resolved,closed','actual_cost'=>'nullable|numeric|min:0','notes'=>'nullable|string|max:1000','resolved_at'=>'nullable|date']));return response()->json(['success'=>true,'message'=>'Issue updated.','data'=>$i]);}
}
