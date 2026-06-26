<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Advertisement extends Model {
    protected $guarded = ['id'];
    protected $casts = ['is_published' => 'boolean'];

    public function owner():    BelongsTo { return $this->belongsTo(Owner::class); }
    public function agent():    BelongsTo { return $this->belongsTo(PropertyAgent::class, 'agent_id'); }
    public function unit():     BelongsTo { return $this->belongsTo(Unit::class); }
    public function property(): BelongsTo { return $this->belongsTo(Property::class); }
    public function bookings(): HasMany   { return $this->hasMany(Booking::class); }
    public function billings(): HasMany   { return $this->hasMany(AdBilling::class); }
    public function images():   HasMany   { return $this->hasMany(AdvertisementImage::class)->orderBy('sort_order'); }

    public function scopePublic($q) {
        return $q->where('is_published', true)->whereIn('status', ['available','reserved']);
    }

    public function statusBadge(): string {
        return match($this->status) {
            'available' => 'success',
            'reserved'  => 'warning',
            'rented'    => 'gray',
            default     => 'danger',
        };
    }
}
