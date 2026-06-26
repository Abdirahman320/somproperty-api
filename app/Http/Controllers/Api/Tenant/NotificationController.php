<?php
namespace App\Http\Controllers\Api\Tenant;
use App\Http\Controllers\Controller;
use App\Models\TenantNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user();
        $notifs = TenantNotification::where('tenant_id', $tenant->id)
            ->with('notification')
            ->latest()
            ->paginate(30);

        $data = $notifs->getCollection()->map(fn($n) => [
            'id'           => $n->id,
            'is_read'      => (bool)$n->is_read,
            'read_at'      => $n->read_at,
            'email_sent'   => (bool)$n->email_sent,
            'delivered_at' => $n->delivered_at,
            'notification' => $n->notification ? [
                'id'      => $n->notification->id,
                'type'    => $n->notification->type,
                'subject' => $n->notification->subject,
                'message' => $n->notification->message,
                'sent_at' => $n->notification->sent_at,
            ] : null,
        ]);

        return response()->json([
            'data'  => $data,
            'total' => $notifs->total(),
            'unread'=> TenantNotification::where('tenant_id',$tenant->id)->where('is_read',false)->count(),
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $tenant = $request->user();
        TenantNotification::where('tenant_id', $tenant->id)->where('id', $id)
            ->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['message' => 'Marked as read.']);
    }

    public function markAllRead(Request $request)
    {
        $tenant = $request->user();
        TenantNotification::where('tenant_id', $tenant->id)->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['message' => 'All marked as read.']);
    }
}
