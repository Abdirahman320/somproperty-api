<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['features' => 'array', 'is_active' => 'boolean'];
    public function owners(): HasMany { return $this->hasMany(Owner::class); }
    public function hasFeature(string $feature): bool {
        return in_array($feature, $this->features ?? [], true);
    }
}
