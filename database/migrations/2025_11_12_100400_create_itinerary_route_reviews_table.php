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
        Schema::create('itinerary_route_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('itinerary_routes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->json('media')->nullable();
            $table->enum('status', ['pending', 'published', 'flagged'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['route_id', 'user_id']);
            $table->index(['route_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_route_reviews');
    }
};

