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
        Schema::create('itinerary_route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('itinerary_routes')->cascadeOnDelete();
            $table->unsignedInteger('sequence')->default(0);
            $table->unsignedInteger('day_number')->nullable();
            $table->string('name');
            $table->string('location_label')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('stay_duration_hours')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            $table->json('tasks')->nullable();
            $table->json('checklists')->nullable();
            $table->timestamp('eta')->nullable();
            $table->timestamp('ata')->nullable();
            $table->timestamp('departure_actual')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('requires_clearance')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['route_id', 'sequence']);
            $table->index(['route_id', 'day_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_route_stops');
    }
};

