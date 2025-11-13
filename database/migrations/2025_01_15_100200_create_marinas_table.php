<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marinas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country');
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('vhf_channel')->nullable();
            $table->text('operating_hours')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->enum('type', ['full_service', 'municipal_port', 'yacht_club', 'anchorage', 'mooring_field', 'dry_stack', 'boatyard'])->default('full_service');
            $table->integer('total_berths')->nullable();
            $table->decimal('max_length_meters', 8, 2)->nullable();
            $table->decimal('max_draft_meters', 8, 2)->nullable();
            $table->decimal('max_beam_meters', 8, 2)->nullable();
            $table->boolean('fuel_diesel')->default(false);
            $table->boolean('fuel_gasoline')->default(false);
            $table->text('fuel_info')->nullable();
            $table->boolean('water_available')->default(true);
            $table->text('water_info')->nullable();
            $table->boolean('electricity_available')->default(true);
            $table->text('electricity_info')->nullable();
            $table->boolean('wifi_available')->default(false);
            $table->text('wifi_info')->nullable();
            $table->boolean('showers_available')->default(false);
            $table->text('showers_info')->nullable();
            $table->boolean('laundry_available')->default(false);
            $table->text('laundry_info')->nullable();
            $table->boolean('maintenance_available')->default(false);
            $table->text('maintenance_info')->nullable();
            $table->boolean('provisioning_available')->default(false);
            $table->text('provisioning_info')->nullable();
            $table->json('amenities')->nullable();
            $table->json('marine_services')->nullable();
            $table->json('safety_security')->nullable();
            $table->text('pricing_info')->nullable();
            $table->enum('value_rating', ['great', 'fair', 'expensive', 'very_expensive', 'ultra_luxury'])->nullable();
            $table->text('location_accessibility')->nullable();
            $table->enum('weather_protection', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('mooring_info')->nullable();
            $table->text('best_time_to_visit')->nullable();
            $table->text('nearby_attractions')->nullable();
            $table->text('customs_regulations')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country', 'type']);
            $table->index('rating_avg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marinas');
    }
};

