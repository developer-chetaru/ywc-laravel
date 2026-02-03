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
            $table->foreignId('quoted_post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignId('quoting_post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->text('quoted_content')->nullable(); // Store the quoted text snippet
            $table->timestamps();

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
