<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yachts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['motor_yacht', 'sailing_yacht', 'explorer', 'catamaran', 'other'])->default('motor_yacht');
            $table->decimal('length_meters', 8, 2)->nullable();
            $table->decimal('length_feet', 8, 2)->nullable();
            $table->year('year_built')->nullable();
            $table->string('flag_registry')->nullable();
            $table->string('home_port')->nullable();
            $table->decimal('beam', 8, 2)->nullable();
            $table->decimal('draft', 8, 2)->nullable();
            $table->integer('gross_tonnage')->nullable();
            $table->string('builder')->nullable();
            $table->string('hull_number')->nullable();
            $table->string('imo_number')->nullable();
            $table->integer('crew_capacity')->nullable();
            $table->integer('guest_capacity')->nullable();
            $table->text('cabin_configuration')->nullable();
            $table->decimal('max_speed', 6, 2)->nullable();
            $table->decimal('cruising_speed', 6, 2)->nullable();
            $table->integer('range_nm')->nullable();
            $table->integer('fuel_capacity_liters')->nullable();
            $table->integer('water_capacity_liters')->nullable();
            $table->text('engine_details')->nullable();
            $table->text('navigation_systems')->nullable();
            $table->text('safety_equipment')->nullable();
            $table->text('amenities')->nullable();
            $table->text('special_features')->nullable();
            $table->enum('status', ['charter', 'private', 'both'])->default('private');
            $table->string('home_region')->nullable();
            $table->text('typical_cruising_grounds')->nullable();
            $table->text('season_schedule')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('recommendation_percentage')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'rating_avg']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yachts');
    }
};

