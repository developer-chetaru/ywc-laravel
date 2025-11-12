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
        Schema::create('itinerary_route_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('itinerary_routes')->cascadeOnDelete();
            $table->foreignId('stop_id')->nullable()->constrained('itinerary_route_stops')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('itinerary_route_comments')->cascadeOnDelete();
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->enum('visibility', ['crew', 'public'])->default('crew');
            $table->enum('status', ['active', 'hidden', 'flagged'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['route_id', 'stop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_route_comments');
    }
};

