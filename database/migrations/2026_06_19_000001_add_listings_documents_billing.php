<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ── TENANT DOCUMENTS ──
           Attached to the TENANT (not the contract) so they survive when a
           contract is terminated/cancelled and are reused when the tenant returns. */
        Schema::create('tenant_documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $t->enum('doc_type', ['passport','police_certificate','national_id','visa','residence_permit','employment_letter','bank_statement','other'])->default('other');
            $t->string('label', 150)->nullable();
            $t->string('file_path');
            $t->string('original_name', 255)->nullable();
            $t->string('mime_type', 100)->nullable();
            $t->unsignedInteger('size_bytes')->default(0);
            $t->date('issued_on')->nullable();
            $t->date('expires_on')->nullable();
            $t->enum('uploaded_by', ['owner','admin'])->default('owner');
            $t->unsignedBigInteger('uploaded_by_id')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['tenant_id','doc_type']);
        });

        /* ── ADVERTISEMENTS ──
           A public listing for a vacant unit. Can be posted by an owner or by an admin.
           Carries the home owner contact details to show to unregistered visitors. */
        Schema::create('advertisements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedBigInteger('property_id')->nullable();
            $t->unsignedBigInteger('unit_id')->nullable();
            $t->string('title', 180);
            $t->text('description')->nullable();
            $t->decimal('monthly_rent', 10, 2)->default(0);
            $t->string('bedrooms', 20)->nullable();
            $t->unsignedTinyInteger('bathrooms')->nullable();
            $t->decimal('area_sqft', 8, 2)->nullable();
            $t->string('city', 100)->nullable();
            $t->string('address', 255)->nullable();
            $t->string('contact_name', 120)->nullable();
            $t->string('contact_phone', 40)->nullable();
            $t->string('contact_email', 150)->nullable();
            $t->string('image_path')->nullable();
            $t->enum('created_by_type', ['owner','admin'])->default('owner');
            $t->unsignedBigInteger('created_by_id')->nullable();
            $t->boolean('is_published')->default(true);
            $t->enum('status', ['available','reserved','rented','closed'])->default('available');
            $t->unsignedInteger('views_count')->default(0);
            $t->timestamps();
            $t->index(['is_published','status']);
        });

        /* ── BOOKINGS / INQUIRIES ──
           Created by unregistered public visitors. No payment, no account required. */
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('advertisement_id')->nullable();
            $t->unsignedBigInteger('owner_id')->nullable();
            $t->unsignedBigInteger('unit_id')->nullable();
            $t->string('name', 120);
            $t->string('email', 150);
            $t->string('phone', 40)->nullable();
            $t->date('preferred_move_in')->nullable();
            $t->text('message')->nullable();
            $t->enum('status', ['new','contacted','viewing_scheduled','closed','cancelled'])->default('new');
            $t->string('reference', 20)->nullable()->unique();
            $t->timestamps();
            $t->index(['owner_id','status']);
            $t->index(['advertisement_id']);
        });

        /* ── ADVERTISEMENT / REPORT BILLING ──
           Admin-registered billing records for advertisements and reports. */
        Schema::create('ad_billings', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('advertisement_id')->nullable();
            $t->unsignedBigInteger('owner_id')->nullable();
            $t->enum('category', ['advertisement','report','feature','other'])->default('advertisement');
            $t->string('description', 200);
            $t->decimal('amount', 10, 2)->default(0);
            $t->string('currency', 8)->default('USD');
            $t->enum('status', ['unpaid','paid','cancelled'])->default('unpaid');
            $t->string('reference_number', 50)->nullable();
            $t->date('billed_on')->nullable();
            $t->date('paid_on')->nullable();
            $t->unsignedBigInteger('created_by_admin')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['status','category']);
        });

        /* ── CONTRACT RENEWAL LINK ── */
        Schema::table('contracts', function (Blueprint $t) {
            $t->unsignedBigInteger('renewed_from_id')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $t) {
            $t->dropColumn('renewed_from_id');
        });
        Schema::dropIfExists('ad_billings');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('tenant_documents');
    }
};
