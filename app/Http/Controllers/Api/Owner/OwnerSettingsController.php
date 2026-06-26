<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class OwnerSettingsController extends Controller {
    public function index(Request $r){$o=$r->user();return response()->json(['success'=>true,'data'=>['id'=>$o->id,'full_name'=>$o->full_name,'company_name'=>$o->company_name,'email'=>$o->email,'phone'=>$o->phone,'max_apartments'=>$o->max_apartments,'plan'=>$o->plan?['name'=>$o->plan->name,'price_monthly'=>$o->plan->price_monthly]:null,'subscription_ends_at'=>$o->subscription_ends_at]]);}
    public function update(Request $r){
        $o=$r->user();$data=$r->validate(['full_name'=>'nullable|string|max:100','company_name'=>'nullable|string|max:150','phone'=>'nullable|string|max:30','password'=>'nullable|string|min:8|confirmed']);
        if(!empty($data['password'])){$data['password_hash']=Hash::make($data['password']);unset($data['password'],$data['password_confirmation']);}
        $o->update($data);return response()->json(['success'=>true,'message'=>'Settings saved.']);
    }
}
