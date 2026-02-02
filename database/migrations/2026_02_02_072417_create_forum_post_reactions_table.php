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
        Schema::create('forum_post_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction_type', 50); // 'like', 'helpful', 'insightful'
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('post_id')
                  ->references('id')
                  ->on('forum_posts')
                  ->onDelete('cascade');

            // Unique constraint: one reaction type per user per post
            $table->unique(['post_id', 'user_id', 'reaction_type'], 'post_user_reaction_unique');

            // Indexes for faster lookups
            $table->index('post_id');
            $table->index('user_id');
            $table->index('reaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_post_reactions');
    }
};
