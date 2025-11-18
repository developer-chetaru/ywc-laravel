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
        Schema::create('work_log_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Time Period
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'yearly', 'custom'])->default('weekly');
            
            // Work Statistics
            $table->integer('total_days_worked')->default(0);
            $table->decimal('total_hours_worked', 10, 2)->default(0);
            $table->decimal('average_hours_per_day', 5, 2)->default(0);
            $table->decimal('total_overtime_hours', 10, 2)->default(0);
            
            // Rest Statistics
            $table->decimal('total_rest_hours', 10, 2)->default(0);
            $table->decimal('average_rest_per_day', 5, 2)->default(0);
            
            // Location Breakdown
            $table->integer('days_at_sea')->default(0);
            $table->integer('days_in_port')->default(0);
            $table->integer('days_on_leave')->default(0);
            $table->integer('days_in_yard')->default(0);
            $table->integer('days_shore_leave')->default(0);
            
            // Compliance Statistics
            $table->integer('compliant_days')->default(0);
            $table->integer('warning_days')->default(0);
            $table->integer('violation_days')->default(0);
            $table->decimal('compliance_percentage', 5, 2)->default(100);
            
            // Sea Service
            $table->integer('qualifying_sea_days')->default(0);
            $table->decimal('qualifying_sea_hours', 10, 2)->default(0);
            
            // Weekly Limits (for MLC compliance)
            $table->decimal('weekly_work_hours', 6, 2)->nullable();
            $table->decimal('weekly_rest_hours', 6, 2)->nullable();
            $table->boolean('weekly_compliant')->default(true);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['user_id', 'period_start', 'period_end', 'period_type']);
            $table->index(['user_id', 'period_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_log_statistics');
    }
};
