<?php
namespace App\Mail;
use App\Models\{Tenant, Owner};
use Illuminate\Mail\Mailable;

class TenantWelcomeMail extends Mailable {
    public function __construct(public Tenant $tenant, public string $password, public Owner $owner) {}
    public function build() {
        return $this->subject('Welcome to '.$this->owner->company_name.' — Your Tenant Portal Access')
            ->view('emails.tenant_welcome');
    }
}
