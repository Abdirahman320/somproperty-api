<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ── Plans (all include core features) ── */
        $coreFeatues = [
            'tenant_portal', 'rent_billing', 'email_notifications',
            'complaint_tracking', 'advanced_reports',
            'water_electric_billing', 'contract_management', 'pdf_exports',
        ];

        // Tiers are defined by an apartment ceiling. The plan limit check uses
        // "units >= max_apartments", so an owner may hold up to and including
        // max_apartments units — i.e. "less than or equal to N units".
        DB::table('plans')->insertOrIgnore([
            [
                'name'           => 'Starter',
                'slug'           => 'starter',
                'price_monthly'  => 20.00,
                'max_apartments' => 14,   // <= 14 units
                'features'       => json_encode($coreFeatues),
                'is_active'      => true,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Pro',
                'slug'           => 'pro',
                'price_monthly'  => 30.00,
                'max_apartments' => 28,   // <= 28 units
                'features'       => json_encode(array_merge($coreFeatues, ['asset_register','technical_issues'])),
                'is_active'      => true,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Premium',
                'slug'           => 'premium',
                'price_monthly'  => 50.00,
                'max_apartments' => 50,   // <= 50 units
                'features'       => json_encode(array_merge($coreFeatues, ['asset_register','technical_issues','bulk_notifications','financial_analytics'])),
                'is_active'      => true,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Maxi-1',
                'slug'           => 'maxi-1',
                'price_monthly'  => 100.00,
                'max_apartments' => 100,  // <= 100 units
                'features'       => json_encode(array_merge($coreFeatues, ['asset_register','technical_issues','bulk_notifications','financial_analytics','multi_property','priority_support'])),
                'is_active'      => true,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Maxi-2',
                'slug'           => 'maxi-2',
                'price_monthly'  => 150.00,
                'max_apartments' => 200,  // <= 200 units
                'features'       => json_encode(array_merge($coreFeatues, ['asset_register','technical_issues','bulk_notifications','financial_analytics','multi_property','priority_support','api_access','custom_branding','dedicated_support'])),
                'is_active'      => true,
                'created_at'     => now(), 'updated_at' => now(),
            ],
        ]);

        /* ── Super Admin — always update so password is known even after DB corruption ── */
        DB::table('admin_users')->updateOrInsert(
            ['email' => 'admin@somproperty.com'],
            [
                'name'          => 'Super Admin',
                'password_hash' => Hash::make('Admin@12345'),
                'role'          => 'super_admin',
                'is_active'     => true,
                'updated_at'    => now(),
            ]
        );

        /* ── Demo Owner ── */
        DB::table('owners')->insertOrIgnore([
            'plan_id'        => 3, // Premium (49 units)
            'company_name'   => 'Green Valley Properties',
            'full_name'      => 'Ahmed Al-Rashid',
            'email'          => 'owner@demo.com',
            'phone'          => '+1-555-0100',
            'password_hash'  => Hash::make('Owner@12345'),
            'max_apartments' => 50,
            'status'         => 'active',
            'created_by'     => 1,
            'created_at'     => now(), 'updated_at' => now(),
        ]);

        /* ── Demo Property Agent (Broker / Dulaal) ── */
        DB::table('property_agents')->insertOrIgnore([
            'full_name'              => 'Hassan Broker',
            'email'                  => 'broker@demo.com',
            'phone'                  => '+971-50-1112233',
            'password_hash'          => Hash::make('Broker@12345'),
            'company_name'           => 'Hassan Real Estate',
            'city'                   => 'Dubai',
            'country'                => 'UAE',
            'subscription_plan'      => 'basic',
            'subscription_price'     => 15.00,
            'subscription_starts_at' => now()->toDateString(),
            'subscription_ends_at'   => now()->addMonth()->toDateString(),
            'status'                 => 'active',
            'created_at'             => now(), 'updated_at' => now(),
        ]);

        $this->call([
            DemoPropertySeeder::class,
        ]);
    }
}
