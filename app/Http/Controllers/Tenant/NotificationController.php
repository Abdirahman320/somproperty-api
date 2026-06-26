<?php
namespace App\Http\Controllers\Tenant;
use App\Http\Controllers\Controller;
use App\Models\TenantNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function index(Request $request) {
        $tenant       = $request->tenant;
        $notifications= TenantNotification::where('tenant_id',$tenant->id)->with('notification')->orderByDesc('id')->paginate(20);
        $unreadCount  = TenantNotification::where('tenant_id',$tenant->id)->where('is_read',false)->count();
        return view('tenant.notifications', compact('notifications','unreadCount','tenant'));
    }
    public function markRead(Request $request, $id) {
        TenantNotification::where('tenant_id',$request->tenant->id)->where('id',$id)->update(['is_read'=>true,'read_at'=>now()]);
        return back();
    }
}
