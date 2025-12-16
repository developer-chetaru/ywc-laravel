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
        Schema::create('crew_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Availability status
            $table->enum('status', ['available_now', 'available_with_notice', 'not_available'])->default('not_available');
            
            // Availability period
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->enum('notice_required', ['immediate', '24_hours', '2_3_days', '1_week'])->nullable();
            
            // Work types available for
            $table->boolean('day_work')->default(false);
            $table->boolean('short_contracts')->default(false); // 2-7 days
            $table->boolean('medium_contracts')->default(false); // 1-4 weeks
            $table->boolean('emergency_cover')->default(false);
            $table->boolean('long_term_seasonal')->default(false); // 3+ months
            
            // Positions available for
            $table->json('available_positions')->nullable(); // e.g., ["2nd Stewardess", "3rd Stewardess", "Laundry"]
            
            // Rates
            $table->decimal('day_rate_min', 10, 2)->nullable();
            $table->decimal('day_rate_max', 10, 2)->nullable();
            $table->decimal('half_day_rate', 10, 2)->nullable();
            $table->decimal('emergency_rate', 10, 2)->nullable(); // Premium for same-day
            $table->decimal('weekly_contract_rate', 10, 2)->nullable(); // If 5+ consecutive days
            $table->boolean('rates_negotiable')->default(true);
            
            // Blocked dates (dates not available)
            $table->json('blocked_dates')->nullable(); // Array of date ranges or single dates
            
            // Location preferences
            $table->string('current_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('search_radius_km')->default(20); // How far willing to travel
            $table->boolean('auto_update_location')->default(true);
            
            // Notification preferences
            $table->boolean('notify_same_day_urgent')->default(true);
            $table->boolean('notify_24_hour_jobs')->default(true);
            $table->boolean('notify_3_day_jobs')->default(true);
            $table->boolean('notify_weekly_contracts')->default(true);
            $table->enum('alert_frequency', ['immediately', 'batched_2_hours', 'daily_digest'])->default('immediately');
            $table->time('quiet_hours_start')->nullable(); // No push notifications during these hours
            $table->time('quiet_hours_end')->nullable();
            
            // Visibility settings
            $table->enum('profile_visibility', ['all_verified', 'previous_employers_only', 'within_radius'])->default('all_verified');
            $table->boolean('show_ratings')->default(true);
            $table->boolean('show_last_worked_date')->default(true);
            $table->boolean('show_job_count')->default(true);
            $table->boolean('show_response_time')->default(true);
            $table->boolean('show_current_vessel')->default(false);
            $table->boolean('show_full_experience')->default(false);
            $table->boolean('allow_direct_booking')->default(false); // Require approval vs direct booking
            
            // Statistics (auto-calculated)
            $table->integer('total_jobs_completed')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('completion_rate_percentage')->default(100); // Jobs completed vs cancelled
            $table->integer('average_response_time_minutes')->nullable();
            $table->integer('repeat_hire_rate_percentage')->nullable(); // Percentage of captains who hired multiple times
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'available_from', 'available_until']);
            $table->index(['latitude', 'longitude']); // For location-based searches
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_availabilities');
    }
};
