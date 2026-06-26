<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /* ── LOCATION FIELDS ON OWNERS ── */
        Schema::table('owners', function (Blueprint $t) {
            $t->string('city', 100)->nullable()->after('phone');
            $t->string('country', 100)->nullable()->after('city');
        });

        /* ── LOCATION FIELDS ON TENANTS ── */
        Schema::table('tenants', function (Blueprint $t) {
            $t->string('city', 100)->nullable()->after('phone');
            $t->string('country', 100)->nullable()->after('city');
        });

        /* ── PROPERTY AGENTS (brokers / Dulaal) ── */
        Schema::create('property_agents', function (Blueprint $t) {
            $t->id();
            $t->string('full_name', 120);
            $t->string('email', 150)->unique();
            $t->string('phone', 40)->nullable();
            $t->string('password_hash');
            $t->string('company_name', 150)->nullable();
            $t->string('city', 100)->nullable();
            $t->string('country', 100)->nullable();
            $t->string('address', 255)->nullable();
            $t->enum('subscription_plan', ['basic', 'pro'])->default('basic');
            $t->decimal('subscription_price', 8, 2)->default(15.00);
            $t->date('subscription_starts_at')->nullable();
            $t->date('subscription_ends_at')->nullable();
            $t->enum('status', ['active', 'suspended', 'pending'])->default('pending');
            $t->unsignedBigInteger('created_by_admin')->nullable();
            $t->timestamps();
            $t->index('status');
            $t->index('city');
        });

        /* ── MULTIPLE IMAGES PER ADVERTISEMENT ── */
        Schema::create('advertisement_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('advertisement_id')->constrained()->cascadeOnDelete();
            $t->string('image_path');
            $t->unsignedTinyInteger('sort_order')->default(0);
            $t->timestamps();
            $t->index(['advertisement_id', 'sort_order']);
        });

        /* ── AGENT_ID ON ADVERTISEMENTS AND BOOKINGS ── */
        Schema::table('advertisements', function (Blueprint $t) {
            $t->unsignedBigInteger('agent_id')->nullable()->after('owner_id');
        });
        Schema::table('bookings', function (Blueprint $t) {
            $t->unsignedBigInteger('agent_id')->nullable()->after('owner_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $t) {
            $t->dropColumn('agent_id');
        });
        Schema::table('advertisements', function (Blueprint $t) {
            $t->dropColumn('agent_id');
        });
        Schema::dropIfExists('advertisement_images');
        Schema::dropIfExists('property_agents');
        Schema::table('tenants', function (Blueprint $t) {
            $t->dropColumn(['city', 'country']);
        });
        Schema::table('owners', function (Blueprint $t) {
            $t->dropColumn(['city', 'country']);
        });
    }
};
