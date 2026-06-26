<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityReading extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['reading_date' => 'date'];
    public function owner(): BelongsTo { return $this->belongsTo(Owner::class); }
    public function unit():  BelongsTo { return $this->belongsTo(Unit::class); }
}
