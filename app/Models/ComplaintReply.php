<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintReply extends Model {
    protected $guarded = ['id'];
    protected $casts   = ['attachments' => 'array'];
    public function complaint(): BelongsTo { return $this->belongsTo(Complaint::class); }
}
