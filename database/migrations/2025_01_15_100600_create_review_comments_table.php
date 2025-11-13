<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // yacht_review or marina_review
            $table->foreignId('review_id'); // yacht_review_id or marina_review_id
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('review_comments')->cascadeOnDelete();
            $table->text('comment');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['review_id', 'is_approved']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_comments');
    }
};

