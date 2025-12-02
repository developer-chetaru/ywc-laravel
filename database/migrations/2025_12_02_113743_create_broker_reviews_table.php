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
        Schema::create('broker_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('review');
            $table->tinyInteger('overall_rating')->unsigned();
            $table->tinyInteger('job_quality_rating')->unsigned()->nullable();
            $table->tinyInteger('communication_rating')->unsigned()->nullable();
            $table->tinyInteger('professionalism_rating')->unsigned()->nullable();
            $table->tinyInteger('fees_transparency_rating')->unsigned()->nullable();
            $table->tinyInteger('support_rating')->unsigned()->nullable();
            $table->boolean('would_use_again')->default(true);
            $table->boolean('would_recommend')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('placement_date')->nullable();
            $table->string('position_placed')->nullable();
            $table->string('yacht_name')->nullable();
            $table->string('placement_timeframe')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['broker_id', 'user_id', 'placement_date']);
            $table->index(['broker_id', 'is_approved']);
            $table->index(['user_id', 'is_anonymous']);
            $table->index('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_reviews');
    }
};
