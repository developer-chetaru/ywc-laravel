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
        Schema::create('training_course_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_course_id')->constrained('training_provider_courses')->onDelete('cascade');
            $table->string('name'); // Location name
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('region')->nullable(); // Caribbean, Mediterranean, etc.
            $table->text('directions')->nullable();
            $table->text('parking_info')->nullable();
            $table->text('accommodation_nearby')->nullable();
            $table->json('photos')->nullable(); // Array of location photos
            $table->boolean('is_primary')->default(false); // Primary location for this course
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_course_locations');
    }
};
