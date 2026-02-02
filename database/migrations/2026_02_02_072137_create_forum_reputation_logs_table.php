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
        Schema::create('forum_reputation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // Can be positive or negative
            $table->string('reason', 255); // e.g., "Thread created", "Helpful reaction", "Best answer"
            $table->string('source_type', 50)->nullable(); // 'thread', 'post', 'reaction', 'best_answer', 'warning'
            $table->unsignedBigInteger('source_id')->nullable(); // ID of the source (thread_id, post_id, etc.)
            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index(['source_type', 'source_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_reputation_logs');
    }
};
