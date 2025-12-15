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
        Schema::create('training_course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_course_id')->constrained('training_provider_courses')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('training_course_locations')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('available_spots')->nullable();
            $table->integer('booked_spots')->default(0);
            $table->boolean('is_full')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->decimal('early_bird_price', 10, 2)->nullable(); // Early bird pricing
            $table->date('early_bird_deadline')->nullable();
            $table->decimal('last_minute_price', 10, 2)->nullable(); // Last minute deals
            $table->boolean('group_booking_available')->default(false);
            $table->integer('group_min_size')->nullable();
            $table->decimal('group_discount_percentage', 5, 2)->nullable();
            $table->timestamps();
            
            // Index for quick date queries
            $table->index(['start_date', 'is_full', 'is_cancelled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_course_schedules');
    }
};
