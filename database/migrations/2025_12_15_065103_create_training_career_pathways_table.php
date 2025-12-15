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
        Schema::create('training_career_pathways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Junior Deckhand Path, Chief Steward/ess Path, etc.
            $table->string('slug')->unique();
            $table->string('starting_position'); // Entry position
            $table->string('target_position'); // Goal position
            $table->text('description')->nullable();
            $table->json('certification_sequence')->nullable(); // Ordered array of certification IDs
            $table->integer('estimated_timeline_months')->nullable();
            $table->decimal('estimated_total_cost', 10, 2)->nullable();
            $table->text('career_benefits')->nullable();
            $table->json('specialized_tracks')->nullable(); // Interior, Deck, Engineering, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_career_pathways');
    }
};
