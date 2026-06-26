<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicalIssue extends Model {
    protected $guarded = ['id'];
    protected $casts   = [
        'scheduled_date' => 'date',
        'resolved_at'    => 'datetime',
        'photo_paths'    => 'array',
    ];
    public function owner():    BelongsTo { return $this->belongsTo(Owner::class); }
    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function unit():     BelongsTo { return $this->belongsTo(Unit::class); }
    public function asset():    BelongsTo { return $this->belongsTo(Asset::class); }
}
