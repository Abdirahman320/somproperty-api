<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Contract extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['start_date'=>'date','end_date'=>'date','terminated_at'=>'datetime','signed_at'=>'datetime'];
    public function owner():  BelongsTo { return $this->belongsTo(Owner::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function unit():   BelongsTo { return $this->belongsTo(Unit::class); }
    public function bills():  HasMany   { return $this->hasMany(TenantBill::class); }
    public function isExpiringSoon(): bool {
        return $this->end_date->diffInDays(now()) <= 30 && $this->end_date->isFuture();
    }
}
