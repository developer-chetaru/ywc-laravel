<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // yacht_review or marina_review
            $table->foreignId('review_id'); // yacht_review_id or marina_review_id
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_helpful')->default(true);
            $table->timestamps();

            $table->unique(['review_id', 'user_id', 'reviewable_type']);
            $table->index(['review_id', 'is_helpful']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_votes');
    }
};

