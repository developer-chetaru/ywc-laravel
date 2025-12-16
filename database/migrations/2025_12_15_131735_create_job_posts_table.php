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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Captain/Poster
            $table->foreignId('yacht_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vessel_verification_id')->nullable()->constrained()->nullOnDelete();
            
            // Job Type
            $table->enum('job_type', ['permanent', 'temporary'])->default('permanent');
            $table->enum('temporary_work_type', ['day_work', 'short_contract', 'medium_contract', 'emergency_cover'])->nullable();
            
            // Position Details
            $table->string('position_title');
            $table->enum('department', ['deck', 'interior', 'engine', 'galley', 'other'])->default('deck');
            $table->string('position_level')->nullable(); // e.g., "2nd Stewardess", "Bosun", "Sous Chef"
            
            // Vessel Information
            $table->enum('vessel_type', ['motor_yacht', 'sailing_yacht', 'explorer', 'catamaran', 'other'])->nullable();
            $table->decimal('vessel_size', 8, 2)->nullable(); // Length in meters
            $table->string('flag')->nullable();
            $table->enum('program_type', ['private', 'charter', 'both'])->default('private');
            $table->text('cruising_regions')->nullable();
            
            // Contract Details (for permanent)
            $table->enum('contract_type', ['permanent_liveaboard', 'permanent_dual_season', 'seasonal', 'rotation', 'trial_permanent'])->nullable();
            $table->string('rotation_schedule')->nullable(); // e.g., "2:1", "3:1", "4:2", "None"
            $table->date('start_date')->nullable();
            $table->string('start_date_flexibility')->nullable(); // "ASAP", "Specific date", "Flexible"
            $table->integer('contract_duration_months')->nullable();
            
            // Temporary Work Details
            $table->date('work_start_date')->nullable();
            $table->date('work_end_date')->nullable();
            $table->time('work_start_time')->nullable();
            $table->time('work_end_time')->nullable();
            $table->integer('total_hours')->nullable();
            $table->enum('urgency_level', ['normal', 'urgent', 'emergency'])->default('normal');
            
            // Location
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('berth_details')->nullable();
            
            // Compensation
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('EUR');
            $table->boolean('salary_negotiable')->default(true);
            $table->decimal('day_rate_min', 10, 2)->nullable();
            $table->decimal('day_rate_max', 10, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            
            // Benefits (for permanent)
            $table->json('benefits')->nullable(); // Medical, training, etc.
            $table->text('additional_benefits')->nullable();
            
            // Requirements
            $table->json('required_certifications')->nullable(); // STCW, ENG1, etc.
            $table->json('preferred_certifications')->nullable();
            $table->integer('min_years_experience')->nullable();
            $table->integer('min_vessel_size_experience')->nullable();
            $table->json('essential_skills')->nullable();
            $table->json('preferred_skills')->nullable();
            $table->json('required_languages')->nullable();
            $table->json('preferred_languages')->nullable();
            $table->text('other_requirements')->nullable();
            $table->text('what_to_bring')->nullable(); // For temporary work
            
            // Job Description
            $table->text('about_position')->nullable();
            $table->text('about_vessel_program')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('ideal_candidate')->nullable();
            $table->integer('crew_size')->nullable();
            $table->text('crew_atmosphere')->nullable();
            
            // Contact & Payment
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('whatsapp_available')->default(false);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'company_process'])->nullable();
            $table->string('payment_timing')->nullable(); // "End of day", "Weekly", etc.
            $table->text('cancellation_policy')->nullable();
            
            // Settings
            $table->enum('contact_preference', ['ywc_only', 'allow_direct_messages', 'provide_email'])->default('ywc_only');
            $table->string('response_timeline')->nullable();
            $table->boolean('public_post')->default(true);
            $table->boolean('allow_search_engine_index')->default(true);
            $table->boolean('notify_matching_crew')->default(true);
            $table->boolean('featured_posting')->default(false);
            
            // Status
            $table->enum('status', ['draft', 'active', 'paused', 'filled', 'closed', 'expired'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('filled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Statistics
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->integer('saved_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['job_type', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['temporary_work_type', 'urgency_level']);
            $table->index(['location', 'latitude', 'longitude']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
