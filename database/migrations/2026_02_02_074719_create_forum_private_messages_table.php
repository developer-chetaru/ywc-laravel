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
        Schema::create('forum_private_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('subject')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('parent_message_id')->nullable(); // For conversation threads
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_deleted_by_sender')->default(false);
            $table->boolean('is_deleted_by_recipient')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('sender_id');
            $table->index('parent_message_id');
            $table->index('is_read');
            $table->index('created_at');
            
            // Foreign key for parent message
            $table->foreign('parent_message_id')
                  ->references('id')
                  ->on('forum_private_messages')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_private_messages');
    }
};
