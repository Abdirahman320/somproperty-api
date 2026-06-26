<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Laravel\Sanctum\HasApiTokens;

class Tenant extends Authenticatable {
    use HasApiTokens;
    protected $guarded = ['id'];
    protected $hidden  = ['password_hash'];
    public function getAuthPassword() { return $this->password_hash; }
    public function owner():    BelongsTo { return $this->belongsTo(Owner::class); }
    public function contracts(): HasMany  { return $this->hasMany(Contract::class); }
    public function bills():    HasMany   { return $this->hasMany(TenantBill::class); }
    public function complaints(): HasMany { return $this->hasMany(Complaint::class); }
    public function notifications(): HasMany { return $this->hasMany(TenantNotification::class); }
    public function activeContract(): HasOne {
        return $this->hasOne(Contract::class)->where('status','active')->latestOfMany();
    }
}
