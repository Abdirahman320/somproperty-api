<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDocument extends Model {
    protected $guarded = ['id'];
    protected $casts = ['issued_on' => 'date', 'expires_on' => 'date'];

    public function owner():  BelongsTo { return $this->belongsTo(Owner::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }

    public function typeLabel(): string {
        return match($this->doc_type) {
            'passport'           => 'Passport',
            'police_certificate' => 'Police Certificate',
            'national_id'        => 'ID Card / National ID',
            'visa'               => 'Visa',
            'residence_permit'   => 'Residence Permit',
            'employment_letter'  => 'Employment Letter',
            'bank_statement'     => 'Bank Statement',
            default              => $this->label ?: 'Other Document',
        };
    }

    public function isExpired(): bool {
        return $this->expires_on && $this->expires_on->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool {
        return $this->expires_on
            && !$this->isExpired()
            && $this->expires_on->lessThanOrEqualTo(now()->addDays($days));
    }

    /** ['text' => '...', 'class' => 'badge-...'] for the expiry badge, or null. */
    public function expiryBadge(): ?array {
        if (!$this->expires_on) {
            return null;
        }
        if ($this->isExpired()) {
            return ['text' => 'Expired ' . $this->expires_on->format('M j, Y'), 'class' => 'danger', 'icon' => 'alert'];
        }
        if ($this->isExpiringSoon()) {
            $days = (int) ceil(now()->diffInDays($this->expires_on, false));
            $when = $days <= 0 ? 'today' : ($days === 1 ? 'in 1 day' : "in {$days} days");
            return ['text' => "Expires {$when}", 'class' => 'warning', 'icon' => 'clock'];
        }
        return ['text' => $this->expires_on->format('M j, Y'), 'class' => 'success', 'icon' => 'calendar'];
    }
}
