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
        Schema::create('verification_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Self-Verified", "Peer-Verified", "Employer-Verified", "Training Provider-Verified", "Official-Verified"
            $table->integer('level')->unique(); // 1-5, where 5 is highest
            $table->text('description')->nullable();
            $table->string('badge_icon')->nullable(); // Icon class name (e.g., "fas fa-check-circle")
            $table->string('badge_color')->default('blue'); // Color for badge display
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_levels');
    }
};
