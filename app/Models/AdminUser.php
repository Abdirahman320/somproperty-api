<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable {
    protected $table   = 'admin_users';
    protected $guarded = ['id'];
    protected $hidden  = ['password_hash'];
    public function getAuthPassword() { return $this->password_hash; }
}
