<?php
namespace App\Mail;
use App\Models\Owner;
use Illuminate\Mail\Mailable;

class OwnerWelcomeMail extends Mailable {
    public function __construct(public Owner $owner, public string $password) {}
    public function build() {
        return $this->subject('Welcome to SOM Property Management — Your Account is Ready')
            ->view('emails.owner_welcome');
    }
}
