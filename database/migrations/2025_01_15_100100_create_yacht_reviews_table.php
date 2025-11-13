<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yacht_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yacht_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('review');
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->tinyInteger('overall_rating')->unsigned();
            $table->tinyInteger('management_rating')->unsigned()->nullable();
            $table->tinyInteger('working_conditions_rating')->unsigned()->nullable();
            $table->tinyInteger('compensation_rating')->unsigned()->nullable();
            $table->tinyInteger('crew_welfare_rating')->unsigned()->nullable();
            $table->tinyInteger('yacht_condition_rating')->unsigned()->nullable();
            $table->tinyInteger('career_development_rating')->unsigned()->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('work_start_date')->nullable();
            $table->date('work_end_date')->nullable();
            $table->string('position_held')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['yacht_id', 'user_id', 'work_start_date']);
            $table->index(['yacht_id', 'is_approved']);
            $table->index(['user_id', 'is_anonymous']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yacht_reviews');
    }
};

