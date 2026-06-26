<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdBilling extends Model {
    protected $table = 'ad_billings';
    protected $guarded = ['id'];
    protected $casts = ['billed_on' => 'date', 'paid_on' => 'date'];

    public function advertisement(): BelongsTo { return $this->belongsTo(Advertisement::class); }
    public function owner(): BelongsTo { return $this->belongsTo(Owner::class); }

    public function statusBadge(): string {
        return match($this->status) {
            'paid'      => 'success',
            'unpaid'    => 'warning',
            default     => 'gray',
        };
    }
}
