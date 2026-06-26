<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class BillingCycle extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['billing_month' => 'date'];
    public function owner(): BelongsTo { return $this->belongsTo(Owner::class); }
    public function bills(): HasMany   { return $this->hasMany(TenantBill::class); }
}
