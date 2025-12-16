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
        Schema::create('job_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->nullable()->constrained()->nullOnDelete(); // For permanent positions
            $table->foreignId('temporary_work_booking_id')->nullable()->constrained()->nullOnDelete(); // For temporary work
            $table->foreignId('rater_user_id')->constrained('users')->cascadeOnDelete(); // Who is giving the rating
            $table->foreignId('rated_user_id')->nullable()->constrained('users')->nullOnDelete(); // Who is being rated (for crew ratings)
            $table->foreignId('rated_yacht_id')->nullable()->constrained('yachts')->nullOnDelete(); // For vessel ratings
            
            // Rating type
            $table->enum('rating_type', ['crew_rates_vessel', 'vessel_rates_crew'])->default('crew_rates_vessel');
            
            // Rating categories (crew rates vessel)
            $table->integer('professionalism_rating')->nullable(); // 1-5
            $table->integer('payment_rating')->nullable(); // 1-5
            $table->integer('management_rating')->nullable(); // 1-5
            $table->integer('vessel_quality_rating')->nullable(); // 1-5
            $table->integer('program_accuracy_rating')->nullable(); // 1-5
            $table->integer('work_life_balance_rating')->nullable(); // 1-5
            
            // Rating categories (vessel rates crew)
            $table->integer('crew_professionalism_rating')->nullable(); // 1-5
            $table->integer('crew_skills_quality_rating')->nullable(); // 1-5
            $table->integer('crew_reliability_rating')->nullable(); // 1-5
            $table->integer('crew_attitude_teamwork_rating')->nullable(); // 1-5
            $table->integer('crew_communication_rating')->nullable(); // 1-5
            
            // Overall rating
            $table->integer('overall_rating'); // 1-5 stars
            
            // Would work/hire again
            $table->enum('would_work_here_again', ['definitely_yes', 'probably_yes', 'maybe', 'probably_not', 'definitely_not'])->nullable();
            $table->enum('would_hire_again', ['definitely_yes', 'probably_yes', 'maybe', 'probably_not', 'definitely_not'])->nullable();
            
            // Written review
            $table->text('review_text')->nullable();
            
            // Privacy
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false); // Verified that actual work occurred
            
            // Moderation
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->foreignId('flagged_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Vessel response (if vessel wants to respond to crew review)
            $table->text('vessel_response')->nullable();
            $table->timestamp('vessel_responded_at')->nullable();
            
            // Private feedback (not shown publicly, only to rated party)
            $table->text('private_feedback')->nullable();
            
            // Actions taken after rating
            $table->boolean('added_to_preferred_list')->default(false);
            $table->boolean('allow_feature_publicly')->default(false); // Allow YWC to feature this review
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['job_post_id', 'rating_type']);
            $table->index(['temporary_work_booking_id', 'rating_type']);
            $table->index(['rated_user_id', 'rating_type']);
            $table->index(['rated_yacht_id', 'rating_type']);
            $table->index('overall_rating');
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_ratings');
    }
};
