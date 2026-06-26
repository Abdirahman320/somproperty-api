<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['payment_date'=>'date'];
    public function bill():   BelongsTo { return $this->belongsTo(TenantBill::class,'tenant_bill_id'); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function owner():  BelongsTo { return $this->belongsTo(Owner::class); }
}
