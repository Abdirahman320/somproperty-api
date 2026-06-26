<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class PropertyAgent extends Authenticatable {
    use HasApiTokens;
    protected $guarded = ['id'];
    protected $hidden  = ['password_hash'];
    protected $casts   = [
        'subscription_starts_at' => 'date',
        'subscription_ends_at'   => 'date',
    ];

    public function getAuthPassword() { return $this->password_hash; }

    public function advertisements(): HasMany { return $this->hasMany(Advertisement::class, 'agent_id'); }
    public function bookings(): HasMany       { return $this->hasMany(Booking::class, 'agent_id'); }

    public function isSubscriptionActive(): bool {
        if (!$this->subscription_ends_at) return false;
        return $this->subscription_ends_at->isFuture();
    }

    public function subscriptionBadge(): string {
        return match($this->subscription_plan) {
            'pro'   => 'info',
            default => 'gray',
        };
    }

    public function statusBadge(): string {
        return match($this->status) {
            'active'    => 'success',
            'pending'   => 'warning',
            'suspended' => 'danger',
            default     => 'gray',
        };
    }
}
