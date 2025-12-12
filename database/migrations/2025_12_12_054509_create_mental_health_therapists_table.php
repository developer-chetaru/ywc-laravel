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
        Schema::create('mental_health_therapists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application_status')->default('pending'); // pending, under_review, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->text('biography')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->json('specializations')->nullable(); // Array of specialization tags
            $table->json('languages_spoken')->nullable(); // Array of languages
            $table->json('therapeutic_approaches')->nullable(); // Array of modalities (CBT, psychodynamic, etc.)
            $table->integer('years_experience')->nullable();
            $table->json('education_history')->nullable(); // Array of {degree, institution, year}
            $table->json('certifications')->nullable(); // Array of certifications
            $table->string('timezone')->nullable();
            $table->json('license_numbers')->nullable(); // Array of {number, jurisdiction, expiry_date}
            $table->text('insurance_information')->nullable();
            $table->decimal('base_hourly_rate', 10, 2)->nullable();
            $table->json('session_type_pricing')->nullable(); // {video: 100, voice: 80, chat: 60, email: 40}
            $table->json('duration_pricing')->nullable(); // {30: 50, 60: 100, 90: 150}
            $table->boolean('sliding_scale_available')->default(false);
            $table->json('sliding_scale_options')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_sessions')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('session_completion_rate', 5, 2)->default(100);
            $table->integer('average_response_time_minutes')->nullable();
            $table->decimal('no_show_rate', 5, 2)->default(0);
            $table->integer('continuing_education_hours')->default(0);
            $table->text('professional_philosophy')->nullable();
            $table->json('areas_of_focus')->nullable(); // PTSD, relationship issues, career stress, etc.
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_therapists');
    }
};
