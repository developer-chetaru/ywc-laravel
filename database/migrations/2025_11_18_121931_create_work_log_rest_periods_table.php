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
        Schema::create('work_log_rest_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained()->cascadeOnDelete();
            
            // Period Information
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration_hours', 5, 2);
            $table->enum('type', ['night_sleep', 'afternoon_nap', 'lunch_break', 'coffee_break', 'other'])->default('night_sleep');
            $table->boolean('is_uninterrupted')->default(true);
            $table->string('location')->nullable(); // e.g., "Crew cabin", "Crew mess"
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('work_log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_log_rest_periods');
    }
};
