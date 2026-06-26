<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model {
    protected $guarded = ['id'];
    protected $casts = ['preferred_move_in' => 'date'];

    public function advertisement(): BelongsTo { return $this->belongsTo(Advertisement::class); }
    public function owner(): BelongsTo { return $this->belongsTo(Owner::class); }
    public function agent(): BelongsTo { return $this->belongsTo(PropertyAgent::class, 'agent_id'); }
    public function unit():  BelongsTo { return $this->belongsTo(Unit::class); }

    public function statusBadge(): string {
        return match($this->status) {
            'new'               => 'info',
            'contacted'         => 'warning',
            'viewing_scheduled' => 'success',
            'closed'            => 'gray',
            default             => 'danger',
        };
    }
}
