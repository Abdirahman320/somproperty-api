<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Property extends Model {
    protected $guarded = ['id'];
    public function owner(): BelongsTo { return $this->belongsTo(Owner::class); }
    public function units(): HasMany   { return $this->hasMany(Unit::class); }
    public function assets(): HasMany  { return $this->hasMany(Asset::class); }
    public function occupancyRate(): float {
        $total = $this->units()->count();
        if ($total === 0) return 0;
        return round($this->units()->where('status','occupied')->count() / $total * 100, 1);
    }
}
