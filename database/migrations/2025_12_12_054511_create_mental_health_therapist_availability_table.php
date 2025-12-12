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
        Schema::create('mental_health_therapist_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('mental_health_therapists')->onDelete('cascade');
            $table->string('day_of_week'); // monday, tuesday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(true);
            $table->date('specific_date')->nullable(); // For one-time availability
            $table->boolean('is_blocked')->default(false); // For vacation/time off
            $table->text('block_reason')->nullable();
            $table->json('session_durations')->nullable(); // [30, 60, 90] minutes available
            $table->integer('buffer_minutes')->default(15); // Time between sessions
            $table->integer('max_daily_sessions')->nullable();
            $table->integer('max_weekly_sessions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_therapist_availability');
    }
};
