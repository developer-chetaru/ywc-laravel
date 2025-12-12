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
        Schema::create('mental_health_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('favorite_type'); // therapist, resource, course
            $table->unsignedBigInteger('favorite_id');
            $table->text('notes')->nullable(); // Why this was favorited
            $table->timestamps();
            
            $table->unique(['user_id', 'favorite_type', 'favorite_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_favorites');
    }
};
