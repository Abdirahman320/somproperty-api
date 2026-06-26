<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Notification, TenantNotification, Tenant};
use App\Services\GmailService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $owner = $request->user();
        $data  = $request->validate([
            'recipients'    => 'required|in:all,overdue,expiring',
            'type'          => 'required|in:billing,overdue,maintenance,announcement,contract,custom',
            'subject'       => 'required|string|max:255',
            'message'       => 'required|string|max:5000',
            'channel_app'   => 'boolean',
            'channel_email' => 'boolean',
        ]);

        // Resolve tenant list
        $tenantQuery = Tenant::where('owner_id', $owner->id)->where('status','active');
        if ($data['recipients'] === 'overdue') {
            $tenantQuery->whereHas('bills', fn($q)=>$q->where('status','overdue'));
        } elseif ($data['recipients'] === 'expiring') {
            $tenantQuery->whereHas('contracts', fn($q)=>$q->where('status','active')->where('end_date','<=',now()->addDays(30)));
        }
        $tenants = $tenantQuery->get();

        // Create master notification
        $channel = match(true) {
            ($data['channel_app'] ?? true) && ($data['channel_email'] ?? true) => 'all',
            ($data['channel_email'] ?? false) => 'email',
            default => 'app',
        };

        $notification = Notification::create([
            'owner_id'     => $owner->id,
            'type'         => $data['type'],
            'channel'      => $channel,
            'subject'      => $data['subject'],
            'message'      => $data['message'],
            'sent_to_count'=> $tenants->count(),
            'status'       => 'sending',
            'sent_at'      => now(),
        ]);

        $gmail = new GmailService();
        $sent  = 0;

        foreach ($tenants as $tenant) {
            // In-app record
            TenantNotification::create([
                'notification_id' => $notification->id,
                'tenant_id'       => $tenant->id,
                'owner_id'        => $owner->id,
                'delivered_at'    => now(),
            ]);

            // Email
            if ($data['channel_email'] ?? true) {
                try {
                    $gmail->sendCustomNotification($tenant, $owner, $data['subject'], $data['message']);
                } catch (\Exception $e) {}
            }
            $sent++;
        }

        $notification->update(['status'=>'sent','sent_to_count'=>$sent]);

        return response()->json(['message' => "$sent notifications sent.", 'count' => $sent]);
    }

    public function history(Request $request)
    {
        $owner = $request->user();
        $history = Notification::where('owner_id', $owner->id)
            ->latest()->take(20)->get()
            ->map(fn($n) => [
                'id'           => $n->id,
                'type'         => $n->type,
                'channel'      => $n->channel,
                'subject'      => $n->subject,
                'sent_to_count'=> $n->sent_to_count,
                'opened_count' => $n->opened_count,
                'status'       => $n->status,
                'sent_at'      => $n->sent_at,
            ]);
        return response()->json(['data' => $history]);
    }
}
