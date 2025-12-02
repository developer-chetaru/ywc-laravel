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
        Schema::create('brokers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('business_name');
            $table->enum('type', [
                'crew_placement_agency',
                'yacht_management',
                'independent_broker',
                'charter_broker'
            ]);
            $table->text('description')->nullable();
            $table->string('primary_location')->nullable();
            $table->text('office_locations')->nullable(); // JSON array
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('specialties')->nullable(); // JSON or comma-separated
            $table->enum('fee_structure', ['free_for_crew', 'crew_pays', 'yacht_pays'])->nullable();
            $table->text('regions_served')->nullable(); // JSON array
            $table->string('years_in_business')->nullable();
            $table->boolean('is_myba_member')->default(false);
            $table->boolean('is_licensed')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->text('certifications')->nullable(); // JSON array
            $table->string('logo')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->decimal('job_quality_rating_avg', 3, 2)->nullable();
            $table->decimal('communication_rating_avg', 3, 2)->nullable();
            $table->decimal('professionalism_rating_avg', 3, 2)->nullable();
            $table->decimal('fees_transparency_rating_avg', 3, 2)->nullable();
            $table->decimal('support_rating_avg', 3, 2)->nullable();
            $table->unsignedInteger('would_use_again_count')->default(0);
            $table->unsignedInteger('would_recommend_count')->default(0);
            $table->string('average_placement_time')->nullable();
            $table->unsignedInteger('positions_per_month')->nullable();
            $table->decimal('success_rate', 5, 2)->nullable(); // percentage
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_verified']);
            $table->index(['primary_location', 'is_verified']);
            $table->index('rating_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brokers');
    }
};
