<?php

namespace App\Services;

use App\Models\TenantBill;
use App\Models\Notification as NotificationModel;
use App\Models\TenantNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GmailService
{
    /**
     * Dynamically configure mailer for a given owner's Gmail SMTP settings,
     * then dispatch the mailable.
     */
    public function sendBillingNotification(TenantBill $bill): bool
    {
        $owner  = $bill->owner;
        $tenant = $bill->tenant;

        if (!$owner->gmail_configured) {
            Log::warning("Owner {$owner->id} has no Gmail configured — skipping email.");
            return false;
        }

        /* Override mailer config at runtime so each owner sends from their own Gmail */
        config([
            'mail.mailers.smtp.host'       => $owner->smtp_host ?? 'smtp.gmail.com',
            'mail.mailers.smtp.port'       => $owner->smtp_port ?? 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.username'   => $owner->smtp_user,
            'mail.mailers.smtp.password'   => decrypt($owner->smtp_pass_encrypted),
            'mail.from.address'            => $owner->smtp_user,
            'mail.from.name'               => $owner->company_name ?? $owner->full_name,
        ]);

        try {
            Mail::to($tenant->email, $tenant->full_name)
                ->send(new \App\Mail\BillingStatementMail($bill));

            $bill->update([
                'notification_sent_at' => now(),
                'notification_count'   => \DB::raw('notification_count + 1'),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send billing email to tenant {$tenant->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a custom notification message to a tenant via email.
     */
    public function sendCustomNotification(
        \App\Models\Tenant $tenant,
        \App\Models\Owner  $owner,
        string $subject,
        string $message
    ): bool {
        if (!$owner->gmail_configured) return false;

        config([
            'mail.mailers.smtp.host'       => $owner->smtp_host ?? 'smtp.gmail.com',
            'mail.mailers.smtp.port'       => $owner->smtp_port ?? 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.username'   => $owner->smtp_user,
            'mail.mailers.smtp.password'   => decrypt($owner->smtp_pass_encrypted),
            'mail.from.address'            => $owner->smtp_user,
            'mail.from.name'               => $owner->company_name ?? $owner->full_name,
        ]);

        try {
            Mail::to($tenant->email, $tenant->full_name)
                ->send(new \App\Mail\CustomNotificationMail($owner, $subject, $message));
            return true;
        } catch (\Exception $e) {
            Log::error("Custom email failed for tenant {$tenant->id}: " . $e->getMessage());
            return false;
        }
    }
}
