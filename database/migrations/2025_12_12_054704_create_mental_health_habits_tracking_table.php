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
        Schema::create('mental_health_habits_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained('mental_health_habits')->onDelete('cascade');
            $table->date('tracked_date');
            $table->boolean('completed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['habit_id', 'tracked_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_habits_tracking');
    }
};
