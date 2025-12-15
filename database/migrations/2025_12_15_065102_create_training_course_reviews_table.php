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
        Schema::create('training_course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_course_id')->constrained('training_provider_courses')->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained('training_course_schedules')->onDelete('set null');
            $table->integer('rating_overall')->default(0); // 1-5 stars
            $table->integer('rating_content')->default(0); // Course content quality
            $table->integer('rating_instructor')->default(0); // Instructor knowledge
            $table->integer('rating_facilities')->default(0); // Facilities and equipment
            $table->integer('rating_value')->default(0); // Value for money
            $table->integer('rating_administration')->default(0); // Administration and organization
            $table->boolean('would_recommend')->default(false);
            $table->text('review_text')->nullable();
            $table->text('liked_most')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('tips_for_students')->nullable();
            $table->date('date_attended')->nullable();
            $table->boolean('is_verified_student')->default(false); // Booked through YWC
            $table->boolean('is_approved')->default(true); // Admin can moderate
            $table->timestamps();
            
            // Ensure one review per user per course
            $table->unique(['user_id', 'provider_course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_course_reviews');
    }
};
