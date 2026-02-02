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
        Schema::create('forum_message_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('forum_private_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['sender', 'recipient'])->default('recipient');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('message_id');
            $table->index('user_id');
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'is_archived']);
            
            // Unique constraint: one participant record per message per user
            $table->unique(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_message_participants');
    }
};
