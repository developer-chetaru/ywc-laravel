<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('role')->nullable()->comment('Yacht crew role (captain, deck, interior, etc.)');
            $table->string('status')->default('pending')->comment('pending, approved, invited');
            $table->text('notes')->nullable()->comment('Admin notes');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->string('source')->nullable()->comment('Where they signed up from (landing_page, linkedin, etc.)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
