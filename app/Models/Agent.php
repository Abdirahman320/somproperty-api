<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['owner_id', 'name', 'email', 'phone', 'password_hash', 'is_active'];
    protected $hidden   = ['password_hash'];

    public function getAuthPassword() { return $this->password_hash; }

    public function owner() { return $this->belongsTo(Owner::class); }
}
