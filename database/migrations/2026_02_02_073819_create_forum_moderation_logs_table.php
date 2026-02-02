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
        Schema::create('forum_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 50); // 'lock', 'unlock', 'pin', 'unpin', 'move', 'merge', 'split', 'delete', 'warn', 'ban', 'unban', 'edit'
            $table->string('target_type', 50); // 'thread', 'post', 'user'
            $table->unsignedBigInteger('target_id');
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // Additional data (e.g., moved_to_category_id)
            $table->timestamps();

            // Indexes
            $table->index('moderator_id');
            $table->index(['target_type', 'target_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_moderation_logs');
    }
};
