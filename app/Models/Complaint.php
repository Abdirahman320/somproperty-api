<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Complaint extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['photo_paths'=>'array','resolved_at'=>'datetime'];
    protected static function boot() {
        parent::boot();
        static::creating(function ($m) {
            $m->ticket_number = 'TKT-'.date('Y').'-'.str_pad(random_int(1,99999),5,'0',STR_PAD_LEFT);
        });
    }
    public function owner():  BelongsTo { return $this->belongsTo(Owner::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function unit():   BelongsTo { return $this->belongsTo(Unit::class); }
    public function replies(): HasMany  { return $this->hasMany(ComplaintReply::class); }
}
