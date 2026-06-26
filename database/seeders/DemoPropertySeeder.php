<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoPropertySeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = DB::table('owners')->where('email', 'owner@demo.com')->value('id');

        if (!$ownerId) return;

        // Skip if demo property already exists (safe to re-run)
        if (DB::table('properties')->where('owner_id', $ownerId)->exists()) return;

        /* ── Demo Property ── */
        $propertyId = DB::table('properties')->insertGetId([
            'owner_id'      => $ownerId,
            'name'          => 'Green Valley Residences',
            'address'       => '123 Green Valley Street',
            'city'          => 'Dubai',
            'country'       => 'UAE',
            'property_type' => 'residential',
            'total_floors'  => 5,
            'status'        => 'active',
            'created_at'    => now(), 'updated_at' => now(),
        ]);

        /* ── Demo Units ── */
        $units = [
            ['unit_number' => '101', 'floor_number' => 1, 'bedrooms' => '1br', 'bathrooms' => 1, 'monthly_rent' => 1200.00, 'status' => 'occupied'],
            ['unit_number' => '102', 'floor_number' => 1, 'bedrooms' => '2br', 'bathrooms' => 2, 'monthly_rent' => 1800.00, 'status' => 'occupied'],
            ['unit_number' => '201', 'floor_number' => 2, 'bedrooms' => '1br', 'bathrooms' => 1, 'monthly_rent' => 1300.00, 'status' => 'vacant'],
            ['unit_number' => '202', 'floor_number' => 2, 'bedrooms' => '3br', 'bathrooms' => 2, 'monthly_rent' => 2500.00, 'status' => 'vacant'],
        ];

        $unitIds = [];
        foreach ($units as $unit) {
            $unitIds[$unit['unit_number']] = DB::table('units')->insertGetId(array_merge($unit, [
                'owner_id'    => $ownerId,
                'property_id' => $propertyId,
                'created_at'  => now(), 'updated_at' => now(),
            ]));
        }

        /* ── Demo Tenants ── */
        $tenant1Id = DB::table('tenants')->insertGetId([
            'owner_id'      => $ownerId,
            'full_name'     => 'Ahmed Hassan',
            'email'         => 'tenant@demo.com',
            'phone'         => '+971-50-1234567',
            'password_hash' => Hash::make('Tenant@12345'),
            'status'        => 'active',
            'created_at'    => now(), 'updated_at' => now(),
        ]);

        $tenant2Id = DB::table('tenants')->insertGetId([
            'owner_id'      => $ownerId,
            'full_name'     => 'Sara Al-Farsi',
            'email'         => 'sara@demo.com',
            'phone'         => '+971-50-7654321',
            'password_hash' => Hash::make('Tenant@12345'),
            'status'        => 'active',
            'created_at'    => now(), 'updated_at' => now(),
        ]);

        /* ── Demo Agent ── */
        DB::table('agents')->insert([
            'owner_id'      => $ownerId,
            'name'          => 'Mohammed Al-Rashid',
            'email'         => 'agent@demo.com',
            'phone'         => '+971-50-9876543',
            'password_hash' => Hash::make('Agent@12345'),
            'is_active'     => true,
            'created_at'    => now(), 'updated_at' => now(),
        ]);

        /* ── Demo Contracts ── */
        DB::table('contracts')->insert([
            [
                'owner_id'         => $ownerId,
                'tenant_id'        => $tenant1Id,
                'unit_id'          => $unitIds['101'],
                'start_date'       => '2025-01-01',
                'end_date'         => '2025-12-31',
                'monthly_rent'     => 1200.00,
                'security_deposit' => 2400.00,
                'status'           => 'active',
                'created_at'       => now(), 'updated_at' => now(),
            ],
            [
                'owner_id'         => $ownerId,
                'tenant_id'        => $tenant2Id,
                'unit_id'          => $unitIds['102'],
                'start_date'       => '2025-03-01',
                'end_date'         => '2026-02-28',
                'monthly_rent'     => 1800.00,
                'security_deposit' => 3600.00,
                'status'           => 'active',
                'created_at'       => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
