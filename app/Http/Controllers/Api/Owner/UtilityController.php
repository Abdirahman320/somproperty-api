<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class UtilityController extends Controller {
    public function index(Request $r){
        $q=UtilityReading::where('owner_id',$r->user()->id)->with('unit');
        if($uid=$r->get('unit_id'))$q->where('unit_id',$uid);
        $rs=$q->latest('reading_date')->paginate(30);
        return response()->json(['success'=>true,'data'=>$rs->getCollection()->map(fn($u)=>['id'=>$u->id,'unit_id'=>$u->unit_id,'unit_number'=>$u->unit?->unit_number,'utility_type'=>$u->utility_type,'reading_date'=>$u->reading_date,'reading_value'=>$u->reading_value,'rate_per_unit'=>$u->rate_per_unit,'created_at'=>$u->created_at]),'meta'=>['total'=>$rs->total(),'current_page'=>$rs->currentPage(),'last_page'=>$rs->lastPage()]]);
    }
}
