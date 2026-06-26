<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /* ── PLANS ── */
        Schema::create('plans', function (Blueprint $t) {
            $t->id();
            $t->string('name', 50);
            $t->string('slug', 50)->unique();
            $t->decimal('price_monthly', 10, 2);
            $t->unsignedInteger('max_apartments');
            $t->longText('features')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        /* ── ADMIN USERS ── */
        Schema::create('admin_users', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('email', 150)->unique();
            $t->string('password_hash');
            $t->enum('role', ['super_admin','support','billing'])->default('support');
            $t->boolean('is_active')->default(true);
            $t->timestamp('last_login_at')->nullable();
            $t->timestamps();
        });

        /* ── OWNERS ── */
        Schema::create('owners', function (Blueprint $t) {
            $t->id();
            $t->foreignId('plan_id')->constrained('plans');
            $t->string('company_name', 150)->nullable();
            $t->string('full_name', 100);
            $t->string('email', 150)->unique();
            $t->string('phone', 30)->nullable();
            $t->string('password_hash');
            $t->unsignedInteger('max_apartments');
            $t->enum('status', ['active','suspended','trial','cancelled'])->default('trial');
            $t->timestamp('trial_ends_at')->nullable();
            $t->timestamp('subscription_starts_at')->nullable();
            $t->timestamp('subscription_ends_at')->nullable();
            $t->boolean('gmail_configured')->default(false);
            $t->string('smtp_host', 150)->nullable();
            $t->unsignedSmallInteger('smtp_port')->default(587);
            $t->string('smtp_user', 150)->nullable();
            $t->string('smtp_pass_encrypted')->nullable();
            $t->string('logo_path')->nullable();
            $t->string('timezone', 50)->default('UTC');
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
        });

        /* ── PROPERTIES ── */
        Schema::create('properties', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $t->string('name', 150);
            $t->text('address');
            $t->string('city', 100)->nullable();
            $t->string('country', 100)->nullable();
            $t->enum('property_type', ['residential','commercial','mixed'])->default('residential');
            $t->unsignedTinyInteger('total_floors')->default(1);
            $t->text('description')->nullable();
            $t->string('image_path')->nullable();
            $t->enum('status', ['active','inactive'])->default('active');
            $t->timestamps();
        });

        /* ── UNITS ── */
        Schema::create('units', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $t->foreignId('property_id')->constrained()->cascadeOnDelete();
            $t->string('unit_number', 30);
            $t->unsignedTinyInteger('floor_number')->default(1);
            $t->enum('bedrooms', ['studio','1br','2br','3br','4br+'])->default('1br');
            $t->unsignedTinyInteger('bathrooms')->default(1);
            $t->decimal('area_sqft', 8, 2)->nullable();
            $t->decimal('monthly_rent', 10, 2);
            $t->longText('amenities')->nullable();
            $t->enum('status', ['vacant','occupied','maintenance','reserved'])->default('vacant');
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->unique(['owner_id','property_id','unit_number']);
        });

        /* ── TENANTS ── */
        Schema::create('tenants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $t->string('full_name', 100);
            $t->string('national_id', 50)->nullable();
            $t->string('email', 150);
            $t->string('phone', 30)->nullable();
            $t->string('emergency_contact', 100)->nullable();
            $t->string('emergency_phone', 30)->nullable();
            $t->string('password_hash')->nullable();
            $t->string('profile_photo')->nullable();
            $t->date('date_of_birth')->nullable();
            $t->enum('status', ['active','inactive','blacklisted'])->default('active');
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->unique(['owner_id','email']);
        });

        /* ── CONTRACTS ── */
        Schema::create('contracts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tenant_id')->constrained('tenants');
            $t->foreignId('unit_id')->constrained('units');
            $t->date('start_date');
            $t->date('end_date');
            $t->decimal('monthly_rent', 10, 2);
            $t->decimal('security_deposit', 10, 2)->default(0);
            $t->unsignedTinyInteger('payment_due_day')->default(1);
            $t->unsignedTinyInteger('grace_period_days')->default(5);
            $t->decimal('late_fee_amount', 8, 2)->default(0);
            $t->string('terms_pdf_path')->nullable();
            $t->enum('status', ['active','expired','terminated','pending'])->default('pending');
            $t->timestamp('terminated_at')->nullable();
            $t->text('termination_reason')->nullable();
            $t->timestamp('signed_at')->nullable();
            $t->timestamps();
        });

        /* ── BILLING CYCLES ── */
        Schema::create('billing_cycles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->unsignedBigInteger('property_id')->nullable();
            $t->date('billing_month');
            $t->enum('status', ['draft','sent','closed'])->default('draft');
            $t->decimal('total_rent', 12, 2)->default(0);
            $t->decimal('total_water', 12, 2)->default(0);
            $t->decimal('total_electric', 12, 2)->default(0);
            $t->decimal('total_other', 12, 2)->default(0);
            $t->timestamps();
        });

        /* ── TENANT BILLS ── */
        Schema::create('tenant_bills', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->foreignId('billing_cycle_id')->constrained('billing_cycles');
            $t->foreignId('contract_id')->constrained('contracts');
            $t->foreignId('tenant_id')->constrained('tenants');
            $t->foreignId('unit_id')->constrained('units');
            $t->date('billing_month');
            $t->date('due_date');
            $t->decimal('rent_amount', 10, 2)->default(0);
            $t->decimal('water_prev_reading', 10, 3)->nullable();
            $t->decimal('water_curr_reading', 10, 3)->nullable();
            $t->decimal('water_consumption', 10, 3)->nullable();
            $t->decimal('water_rate', 8, 4)->nullable();
            $t->decimal('water_amount', 10, 2)->default(0);
            $t->decimal('electric_prev_reading', 10, 3)->nullable();
            $t->decimal('electric_curr_reading', 10, 3)->nullable();
            $t->decimal('electric_consumption', 10, 3)->nullable();
            $t->decimal('electric_rate', 8, 4)->nullable();
            $t->decimal('electric_amount', 10, 2)->default(0);
            $t->decimal('parking_amount', 10, 2)->default(0);
            $t->longText('other_charges')->nullable();
            $t->decimal('late_fee', 10, 2)->default(0);
            $t->decimal('discount_amount', 10, 2)->default(0);
            $t->decimal('total_amount', 10, 2);
            $t->decimal('amount_paid', 10, 2)->default(0);
            $t->enum('status', ['pending','partially_paid','paid','overdue','waived'])->default('pending');
            $t->timestamp('notification_sent_at')->nullable();
            $t->unsignedTinyInteger('notification_count')->default(0);
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        /* ── PAYMENTS ── */
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->foreignId('tenant_bill_id')->constrained('tenant_bills');
            $t->foreignId('tenant_id')->constrained('tenants');
            $t->decimal('amount', 10, 2);
            $t->enum('payment_method', ['cash','bank_transfer','check','online','other']);
            $t->string('reference_number', 100)->nullable();
            $t->date('payment_date');
            $t->unsignedBigInteger('recorded_by');
            $t->string('receipt_number', 50)->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        /* ── UTILITY READINGS ── */
        Schema::create('utility_readings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->foreignId('unit_id')->constrained('units');
            $t->enum('utility_type', ['water','electric','gas']);
            $t->date('reading_date');
            $t->decimal('reading_value', 12, 3);
            $t->decimal('rate_per_unit', 8, 4);
            $t->string('photo_path')->nullable();
            $t->unsignedBigInteger('recorded_by')->nullable();
            $t->timestamps();
        });

        /* ── NOTIFICATIONS ── */
        Schema::create('notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->unsignedBigInteger('tenant_id')->nullable();
            $t->enum('type', ['billing','overdue','maintenance','announcement','contract','welcome','custom']);
            $t->enum('channel', ['app','email','all'])->default('all');
            $t->string('subject')->nullable();
            $t->text('message');
            $t->unsignedInteger('sent_to_count')->default(0);
            $t->unsignedInteger('opened_count')->default(0);
            $t->enum('status', ['draft','sending','sent','failed'])->default('draft');
            $t->timestamp('sent_at')->nullable();
            $t->timestamp('scheduled_at')->nullable();
            $t->timestamps();
        });

        Schema::create('tenant_notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('notification_id')->constrained('notifications');
            $t->foreignId('tenant_id')->constrained('tenants');
            $t->foreignId('owner_id')->constrained();
            $t->boolean('is_read')->default(false);
            $t->timestamp('read_at')->nullable();
            $t->boolean('email_sent')->default(false);
            $t->boolean('email_opened')->default(false);
            $t->timestamp('delivered_at')->nullable();
        });

        /* ── COMPLAINTS ── */
        Schema::create('complaints', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->foreignId('tenant_id')->constrained('tenants');
            $t->foreignId('unit_id')->constrained('units');
            $t->string('ticket_number', 20)->unique();
            $t->string('title', 200);
            $t->text('description');
            $t->enum('category', ['plumbing','electrical','structural','noise','cleaning','furniture','security','parking','other']);
            $t->enum('priority', ['low','medium','high','emergency'])->default('medium');
            $t->enum('status', ['open','assigned','in_progress','resolved','closed','rejected'])->default('open');
            $t->string('assigned_to', 100)->nullable();
            $t->longText('photo_paths')->nullable();
            $t->text('resolution_notes')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->unsignedTinyInteger('tenant_rating')->nullable();
            $t->timestamps();
        });

        Schema::create('complaint_replies', function (Blueprint $t) {
            $t->id();
            $t->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $t->enum('sender_type', ['owner','tenant']);
            $t->unsignedBigInteger('sender_id');
            $t->text('message');
            $t->longText('attachments')->nullable();
            $t->timestamps();
        });

        /* ── ASSETS ── */
        Schema::create('assets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->foreignId('property_id')->constrained('properties');
            $t->unsignedBigInteger('unit_id')->nullable();
            $t->string('name', 150);
            $t->enum('category', ['mechanical','electrical','plumbing','electronic','furniture','vehicle','other']);
            $t->string('brand', 100)->nullable();
            $t->string('model', 100)->nullable();
            $t->string('serial_number', 100)->nullable();
            $t->date('purchase_date')->nullable();
            $t->decimal('purchase_value', 10, 2)->nullable();
            $t->decimal('current_value', 10, 2)->nullable();
            $t->date('warranty_expires_at')->nullable();
            $t->string('location', 200)->nullable();
            $t->enum('status', ['operational','maintenance','under_repair','disposed','lost'])->default('operational');
            $t->date('last_maintenance_at')->nullable();
            $t->date('next_maintenance_at')->nullable();
            $t->string('photo_path')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        /* ── TECHNICAL ISSUES ── */
        Schema::create('technical_issues', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained();
            $t->foreignId('property_id')->constrained('properties');
            $t->unsignedBigInteger('unit_id')->nullable();
            $t->unsignedBigInteger('asset_id')->nullable();
            $t->string('title', 200);
            $t->text('description');
            $t->enum('priority', ['low','medium','high','critical'])->default('medium');
            $t->enum('status', ['open','assigned','in_progress','resolved','closed'])->default('open');
            $t->string('assigned_to', 100)->nullable();
            $t->string('contractor_name', 100)->nullable();
            $t->string('contractor_phone', 30)->nullable();
            $t->decimal('estimated_cost', 10, 2)->nullable();
            $t->decimal('actual_cost', 10, 2)->nullable();
            $t->enum('reported_by', ['owner','tenant','system'])->default('owner');
            $t->unsignedBigInteger('reporter_id')->nullable();
            $t->date('scheduled_date')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->text('resolution_notes')->nullable();
            $t->longText('photo_paths')->nullable();
            $t->timestamps();
        });

        /* ── AUDIT LOG ── */
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('owner_id')->nullable();
            $t->enum('user_type', ['admin','owner','tenant']);
            $t->unsignedBigInteger('user_id');
            $t->string('action', 100);
            $t->string('resource_type', 50)->nullable();
            $t->unsignedBigInteger('resource_id')->nullable();
            $t->longText('old_values')->nullable();
            $t->longText('new_values')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->string('user_agent')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['owner_id','created_at']);
            $t->index(['resource_type','resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('technical_issues');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('complaint_replies');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('tenant_notifications');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('utility_readings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('tenant_bills');
        Schema::dropIfExists('billing_cycles');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('units');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('owners');
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('plans');
    }
};
