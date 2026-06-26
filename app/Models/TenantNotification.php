<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantNotification extends Model {
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts   = ['is_read' => 'boolean', 'email_sent' => 'boolean', 'email_opened' => 'boolean', 'read_at' => 'datetime', 'delivered_at' => 'datetime'];
    public function notification(): BelongsTo { return $this->belongsTo(Notification::class); }
    public function tenant():       BelongsTo { return $this->belongsTo(Tenant::class); }
    public function owner():        BelongsTo { return $this->belongsTo(Owner::class); }
}
