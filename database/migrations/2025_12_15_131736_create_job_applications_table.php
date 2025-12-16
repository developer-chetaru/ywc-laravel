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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Applicant/Crew member
            
            // Application status
            $table->enum('status', ['submitted', 'viewed', 'reviewed', 'shortlisted', 'interview_requested', 'interview_scheduled', 'interviewed', 'offer_sent', 'offer_accepted', 'offer_declined', 'withdrawn', 'declined', 'hired'])->default('submitted');
            
            // Match score calculated by algorithm
            $table->decimal('match_score', 5, 2)->nullable(); // 0-100%
            
            // Screening questions responses
            $table->json('screening_responses')->nullable();
            
            // Cover message
            $table->text('cover_message')->nullable();
            
            // Additional documents
            $table->json('attached_documents')->nullable(); // Paths to uploaded documents
            
            // Timeline tracking
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->integer('view_duration_seconds')->nullable(); // How long captain viewed profile
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('shortlisted_at')->nullable();
            $table->timestamp('interview_requested_at')->nullable();
            $table->timestamp('interview_scheduled_at')->nullable();
            $table->timestamp('interviewed_at')->nullable();
            $table->timestamp('offer_sent_at')->nullable();
            $table->timestamp('offer_responded_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('hired_at')->nullable();
            
            // Captain's evaluation (private)
            $table->integer('captain_rating')->nullable(); // 1-5 stars (private to captain)
            $table->text('captain_notes')->nullable(); // Private notes
            $table->string('folder')->nullable(); // Custom folder organization
            
            // Notification preferences
            $table->boolean('notify_on_view')->default(true);
            $table->boolean('notify_on_status_change')->default(true);
            $table->boolean('notify_on_message')->default(true);
            
            // Withdrawal/decline reasons
            $table->text('withdrawal_reason')->nullable();
            $table->text('decline_feedback')->nullable(); // Feedback from captain
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['job_post_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('match_score');
            $table->unique(['job_post_id', 'user_id']); // One application per crew per job
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
