<?php
namespace App\Mail;
use App\Models\TenantBill;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillingStatementMail extends Mailable {
    use SerializesModels;
    public function __construct(public TenantBill $bill) {}
    public function build() {
        return $this->subject('Your billing statement for '.$this->bill->billing_month->format('F Y'))
            ->view('emails.billing_statement');
    }
}
