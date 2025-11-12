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
        Schema::create('itinerary_weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stop_id')->constrained('itinerary_route_stops')->cascadeOnDelete();
            $table->date('forecast_date');
            $table->json('payload');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['stop_id', 'forecast_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_weather_snapshots');
    }
};

