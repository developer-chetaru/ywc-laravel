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
        Schema::create('rally_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rally_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('rally_comments')->nullOnDelete();
            $table->text('comment');
            $table->boolean('is_pinned')->default(false);
            $table->integer('likes_count')->default(0);
            $table->timestamps();

            $table->index(['rally_id', 'created_at']);
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rally_comments');
    }
};
