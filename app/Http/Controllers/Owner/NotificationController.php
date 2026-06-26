<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Notification, TenantNotification, Tenant};
use App\Services\GmailService;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function index(Request $request) {
        $owner   = $request->owner;
        $history = Notification::where('owner_id', $owner->id)->latest()->take(20)->get();
        return view('owner.notifications.index', compact('history'));
    }
    public function send(Request $request) {
        $owner = $request->owner;
        $data  = $request->validate([
            'recipients' => 'required|in:all,overdue,expiring',
            'type'       => 'required|in:billing,overdue,maintenance,announcement,contract,custom',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string|max:5000',
        ]);
        $q = Tenant::where('owner_id', $owner->id)->where('status','active');
        if ($data['recipients'] === 'overdue')   $q->whereHas('bills', fn($b) => $b->where('status','overdue'));
        if ($data['recipients'] === 'expiring')  $q->whereHas('contracts', fn($c) => $c->where('status','active')->where('end_date','<=',now()->addDays(30)));
        $tenants = $q->get();

        $notification = Notification::create([
            'owner_id' => $owner->id, 'type' => $data['type'],
            'channel'  => ($request->channel_email ? 'all' : 'app'),
            'subject'  => $data['subject'], 'message' => $data['message'],
            'sent_to_count' => $tenants->count(), 'status' => 'sending', 'sent_at' => now(),
        ]);
        $gmail = new GmailService();
        foreach ($tenants as $tenant) {
            TenantNotification::create(['notification_id' => $notification->id, 'tenant_id' => $tenant->id, 'owner_id' => $owner->id, 'delivered_at' => now()]);
            if ($request->boolean('channel_email')) {
                try { $gmail->sendCustomNotification($tenant, $owner, $data['subject'], $data['message']); } catch (\Exception $e) {}
            }
        }
        $notification->update(['status' => 'sent']);
        return back()->with('success', "Notifications sent to {$tenants->count()} tenants.");
    }
}
