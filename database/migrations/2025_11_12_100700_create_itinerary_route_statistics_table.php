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
        Schema::create('itinerary_route_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('itinerary_routes')->cascadeOnDelete();
            $table->unsignedBigInteger('views_total')->default(0);
            $table->unsignedBigInteger('views_unique')->default(0);
            $table->unsignedBigInteger('copies_total')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->json('regions_breakdown')->nullable();
            $table->json('analytics')->nullable();
            $table->timestamps();

            $table->unique('route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_route_statistics');
    }
};

