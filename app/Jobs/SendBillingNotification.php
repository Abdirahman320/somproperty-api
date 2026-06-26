<?php
namespace App\Jobs;
use App\Models\TenantBill;
use App\Services\GmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class SendBillingNotification implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public function __construct(public TenantBill $bill) {}
    public function handle(GmailService $gmail): void {
        $gmail->sendBillingNotification($this->bill);
        // Also create in-app notification record
        \App\Models\TenantNotification::create([
            'notification_id' => \App\Models\Notification::create([
                'owner_id' => $this->bill->owner_id,
                'tenant_id'=> $this->bill->tenant_id,
                'type'     => 'billing',
                'channel'  => 'all',
                'subject'  => 'Billing statement for '.$this->bill->billing_month->format('F Y'),
                'message'  => 'Your bill of $'.number_format($this->bill->total_amount,2).' is due on '.$this->bill->due_date->format('M j, Y'),
                'status'   => 'sent',
                'sent_at'  => now(),
            ])->id,
            'tenant_id'   => $this->bill->tenant_id,
            'owner_id'    => $this->bill->owner_id,
            'email_sent'  => true,
            'delivered_at'=> now(),
        ]);
    }
}
