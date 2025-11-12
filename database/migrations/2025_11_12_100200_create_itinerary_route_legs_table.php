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
        Schema::create('itinerary_route_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('itinerary_routes')->cascadeOnDelete();
            $table->foreignId('from_stop_id')->nullable()->constrained('itinerary_route_stops')->nullOnDelete();
            $table->foreignId('to_stop_id')->nullable()->constrained('itinerary_route_stops')->nullOnDelete();
            $table->unsignedInteger('sequence')->default(0);
            $table->decimal('distance_nm', 8, 2)->default(0);
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->decimal('average_speed_knots', 5, 2)->nullable();
            $table->json('sailing_notes')->nullable();
            $table->json('weather_window')->nullable();
            $table->json('metrics')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['route_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_route_legs');
    }
};

