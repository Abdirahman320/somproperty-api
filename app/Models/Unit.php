<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Unit extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['amenities' => 'array'];
    public function owner():    BelongsTo { return $this->belongsTo(Owner::class); }
    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function contracts(): HasMany  { return $this->hasMany(Contract::class); }
    public function bills():    HasMany   { return $this->hasMany(TenantBill::class); }
    public function activeContract(): HasOne {
        return $this->hasOne(Contract::class)->where('status','active')->latestOfMany();
    }
}
