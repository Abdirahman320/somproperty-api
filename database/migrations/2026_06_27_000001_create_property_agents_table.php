<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('property_agents');
    }
};
