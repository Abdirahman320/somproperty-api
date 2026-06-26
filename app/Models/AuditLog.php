<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model {
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts   = ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];

    public static function record(string $action, string $userType, int $userId, ?int $ownerId = null, ?string $resourceType = null, ?int $resourceId = null, array $old = [], array $new = []): void {
        static::create([
            'owner_id'      => $ownerId,
            'user_type'     => $userType,
            'user_id'       => $userId,
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'old_values'    => $old ?: null,
            'new_values'    => $new ?: null,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}
