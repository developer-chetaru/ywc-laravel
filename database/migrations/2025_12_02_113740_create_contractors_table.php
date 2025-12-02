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
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('business_name')->nullable();
            $table->enum('category', [
                'technical_services',
                'refit_repair',
                'equipment_supplier',
                'professional_services',
                'crew_services'
            ]);
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('specialties')->nullable(); // JSON or comma-separated
            $table->text('languages')->nullable(); // JSON or comma-separated
            $table->boolean('emergency_service')->default(false);
            $table->string('response_time')->nullable();
            $table->string('service_area')->nullable();
            $table->enum('price_range', ['€', '€€', '€€€', '€€€€'])->nullable();
            $table->string('logo')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->decimal('quality_rating_avg', 3, 2)->nullable();
            $table->decimal('professionalism_rating_avg', 3, 2)->nullable();
            $table->decimal('pricing_rating_avg', 3, 2)->nullable();
            $table->decimal('timeliness_rating_avg', 3, 2)->nullable();
            $table->unsignedInteger('recommendation_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category', 'is_verified']);
            $table->index(['location', 'country']);
            $table->index('rating_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
