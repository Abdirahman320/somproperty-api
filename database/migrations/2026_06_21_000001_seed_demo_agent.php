<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void
    {
        DB::table('property_agents')->insertOrIgnore([
            'full_name'          => 'SOM Agent',
            'email'              => 'agent@demo.com',
            'phone'              => '+1-555-0200',
            'password_hash'      => Hash::make('Agent@12345'),
            'company_name'       => 'SOM Realty',
            'city'               => 'Mogadishu',
            'country'            => 'Somalia',
            'subscription_plan'  => 'pro',
            'subscription_price' => 15.00,
            'subscription_starts_at' => now()->toDateString(),
            'subscription_ends_at'   => now()->addYear()->toDateString(),
            'status'             => 'active',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('property_agents')->where('email', 'agent@demo.com')->delete();
    }
};
