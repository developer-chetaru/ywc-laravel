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
        Schema::create('mental_health_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // mental_health, career, relationships, etc.
            $table->date('target_date')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->json('milestones')->nullable(); // Array of sub-goals
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_goals');
    }
};
