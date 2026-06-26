<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class TenantBill extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['billing_month'=>'date','due_date'=>'date','notification_sent_at'=>'datetime','other_charges'=>'array'];
    public function owner():    BelongsTo { return $this->belongsTo(Owner::class); }
    public function tenant():   BelongsTo { return $this->belongsTo(Tenant::class); }
    public function unit():     BelongsTo { return $this->belongsTo(Unit::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function payments(): HasMany   { return $this->hasMany(Payment::class,'tenant_bill_id'); }
    public function getBalanceDueAttribute(): float { return max(0, $this->total_amount - $this->amount_paid); }
    public function isOverdue(): bool { return $this->due_date->isPast() && $this->status !== 'paid'; }
}
