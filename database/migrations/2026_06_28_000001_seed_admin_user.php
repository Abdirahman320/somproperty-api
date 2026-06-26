<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void
    {
        DB::table('admin_users')->insertOrIgnore([
            'name'          => 'Super Admin',
            'email'         => 'admin@somproperty.com',
            'password_hash' => Hash::make('Admin@12345'),
            'role'          => 'super_admin',
            'is_active'     => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('admin_users')->where('email', 'admin@somproperty.com')->delete();
    }
};
