<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsTo};
use Laravel\Sanctum\HasApiTokens;

class Owner extends Authenticatable {
    use HasApiTokens;
    protected $guarded = ['id'];
    protected $hidden  = ['password_hash'];
    protected $casts   = [
        'trial_ends_at'          => 'datetime',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at'   => 'datetime',
        'gmail_configured'       => 'boolean',
    ];

    public function getAuthPassword() { return $this->password_hash; }

    public function plan():       BelongsTo { return $this->belongsTo(Plan::class); }
    public function properties(): HasMany   { return $this->hasMany(Property::class); }
    public function units():      HasMany   { return $this->hasMany(Unit::class); }
    public function tenants():    HasMany   { return $this->hasMany(Tenant::class); }
    public function contracts():  HasMany   { return $this->hasMany(Contract::class); }
    public function bills():      HasMany   { return $this->hasMany(TenantBill::class); }
    public function assets():     HasMany   { return $this->hasMany(Asset::class); }
    public function complaints(): HasMany   { return $this->hasMany(Complaint::class); }

    public function isAtPlanLimit(): bool {
        return $this->units()->whereNotIn('status',['disposed'])->count() >= $this->max_apartments;
    }
    public function usedApartments(): int {
        return $this->units()->whereNotIn('status',['disposed'])->count();
    }
}
