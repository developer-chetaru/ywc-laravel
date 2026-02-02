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
        Schema::create('forum_thread_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('thread_id');
            $table->string('role_name', 100); // e.g., 'Captain', 'Chief engineer', 'Chef', 'Deckhand', 'Stewardess'
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('thread_id')
                  ->references('id')
                  ->on('forum_threads')
                  ->onDelete('cascade');

            // Unique constraint: one role per thread
            $table->unique(['thread_id', 'role_name'], 'thread_role_unique');

            // Index for faster lookups
            $table->index('thread_id');
            $table->index('role_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_thread_roles');
    }
};
