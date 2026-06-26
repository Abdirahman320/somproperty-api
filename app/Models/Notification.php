<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Notification extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['sent_at' => 'datetime', 'scheduled_at' => 'datetime'];
    public function owner():      BelongsTo { return $this->belongsTo(Owner::class); }
    public function recipients(): HasMany   { return $this->hasMany(TenantNotification::class); }
}
