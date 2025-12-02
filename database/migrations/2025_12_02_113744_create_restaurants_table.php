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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', [
                'restaurant',
                'bar',
                'cafe',
                'shop',
                'service'
            ]);
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('cuisine_type')->nullable(); // For restaurants
            $table->enum('price_range', ['€', '€€', '€€€', '€€€€'])->nullable();
            $table->text('opening_hours')->nullable(); // JSON
            $table->boolean('crew_friendly')->default(false);
            $table->boolean('crew_discount')->default(false);
            $table->text('crew_discount_details')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('recommendation_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_verified']);
            $table->index(['city', 'country']);
            $table->index('rating_avg');
            $table->index('crew_friendly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
