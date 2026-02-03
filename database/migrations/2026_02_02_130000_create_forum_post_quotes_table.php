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
        Schema::create('forum_post_quotes', function (Blueprint $table) {
            $table->id();
            // forum_posts uses unsigned integer, not bigInteger
            $table->unsignedInteger('quoted_post_id');
            $table->unsignedInteger('quoting_post_id');
            $table->text('quoted_content')->nullable(); // Store the quoted text snippet
            $table->timestamps();

            // Add foreign key constraints with explicit column type
            $table->foreign('quoted_post_id')
                  ->references('id')
                  ->on('forum_posts')
                  ->onDelete('cascade');
            
            $table->foreign('quoting_post_id')
                  ->references('id')
                  ->on('forum_posts')
                  ->onDelete('cascade');

            $table->index('quoted_post_id');
            $table->index('quoting_post_id');
            $table->unique(['quoted_post_id', 'quoting_post_id']); // Prevent duplicate quotes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_post_quotes');
    }
};
