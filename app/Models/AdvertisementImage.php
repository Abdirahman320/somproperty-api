<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementImage extends Model {
    protected $guarded = ['id'];

    public function advertisement(): BelongsTo { return $this->belongsTo(Advertisement::class); }
}
