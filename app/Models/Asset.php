<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['purchase_date'=>'date','warranty_expires_at'=>'date','last_maintenance_at'=>'date','next_maintenance_at'=>'date'];
    public function owner():    BelongsTo { return $this->belongsTo(Owner::class); }
    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
}
