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
        Schema::create('mental_health_mood_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tracked_date');
            $table->integer('mood_rating')->nullable(); // 1-10 scale
            $table->string('primary_mood')->nullable(); // happy, sad, anxious, etc.
            $table->json('secondary_emotions')->nullable();
            $table->integer('energy_level')->nullable(); // 1-10
            $table->integer('sleep_quality')->nullable(); // 1-10
            $table->integer('stress_level')->nullable(); // 1-10
            $table->json('physical_symptoms')->nullable(); // headaches, fatigue, etc.
            $table->json('medications')->nullable(); // Array of medications taken
            $table->text('trigger_notes')->nullable();
            $table->time('tracked_time')->nullable();
            $table->boolean('is_quick_checkin')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'tracked_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_mood_tracking');
    }
};
