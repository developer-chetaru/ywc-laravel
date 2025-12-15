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
        Schema::create('training_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Provider account
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->text('company_overview')->nullable();
            $table->integer('years_in_operation')->nullable();
            $table->json('accreditations')->nullable(); // Array of accreditations
            $table->json('certifications')->nullable(); // Provider's own certifications
            $table->text('training_facilities')->nullable();
            $table->json('instructor_qualifications')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('social_media_links')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0); // Average rating out of 5
            $table->integer('total_reviews')->default(0);
            $table->decimal('pass_rate', 5, 2)->nullable(); // Percentage
            $table->integer('total_students_trained')->default(0);
            $table->integer('total_students_ywc')->default(0); // Through YWC platform
            $table->integer('response_time_hours')->nullable(); // Average response time
            $table->decimal('cancellation_rate', 5, 2)->nullable();
            $table->boolean('is_verified_partner')->default(false); // YWC verified partner
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_providers');
    }
};
