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
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->boolean('is_question')->default(false)->after('locked');
            $table->unsignedInteger('best_answer_post_id')->nullable()->after('is_question');
            
            // Foreign key constraint
            $table->foreign('best_answer_post_id')
                  ->references('id')
                  ->on('forum_posts')
                  ->onDelete('set null');
                  
            // Index for faster queries
            $table->index('is_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropForeign(['best_answer_post_id']);
            $table->dropIndex(['is_question']);
            $table->dropColumn(['is_question', 'best_answer_post_id']);
        });
    }
};
