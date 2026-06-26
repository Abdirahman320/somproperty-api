<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
            $t->string('name', 100);
            $t->string('email', 150)->unique();
            $t->string('phone', 30)->nullable();
            $t->string('password_hash');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
