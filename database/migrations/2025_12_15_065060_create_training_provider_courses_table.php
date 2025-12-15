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
        Schema::create('training_provider_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_id')->constrained('training_certifications')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('training_providers')->onDelete('cascade');
            $table->decimal('price', 10, 2); // Standard price
            $table->decimal('ywc_discount_percentage', 5, 2)->default(20.00); // Default 20% discount
            $table->integer('duration_days');
            $table->integer('duration_hours')->nullable();
            $table->integer('class_size_max')->nullable();
            $table->string('language_of_instruction')->default('English');
            $table->enum('format', ['in-person', 'online', 'hybrid', 'self-paced'])->default('in-person');
            $table->text('course_structure')->nullable(); // JSON or text description
            $table->json('daily_schedule')->nullable(); // Day-by-day breakdown
            $table->json('learning_outcomes')->nullable();
            $table->json('assessment_methods')->nullable();
            $table->json('materials_included')->nullable(); // Manuals, uniforms, equipment
            $table->boolean('accommodation_included')->default(false);
            $table->text('accommodation_details')->nullable();
            $table->boolean('meals_included')->default(false);
            $table->text('meals_details')->nullable(); // Lunch, refreshments, etc.
            $table->boolean('parking_included')->default(false);
            $table->boolean('transport_included')->default(false);
            $table->boolean('re_sits_included')->default(false);
            $table->text('special_features')->nullable(); // What makes this course unique
            $table->text('booking_url')->nullable(); // External booking link
            $table->string('ywc_tracking_code')->nullable(); // Unique code for YWC referrals
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('click_through_count')->default(0);
            $table->integer('booking_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_admin_approval')->default(false); // For new certifications
            $table->timestamps();
            
            // Ensure one provider can only have one course per certification
            $table->unique(['certification_id', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_provider_courses');
    }
};
