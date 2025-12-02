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
        Schema::create('restaurant_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('review');
            $table->tinyInteger('overall_rating')->unsigned();
            $table->tinyInteger('food_rating')->unsigned()->nullable();
            $table->tinyInteger('service_rating')->unsigned()->nullable();
            $table->tinyInteger('atmosphere_rating')->unsigned()->nullable();
            $table->tinyInteger('value_rating')->unsigned()->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('visit_date')->nullable();
            $table->text('crew_tips')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['restaurant_id', 'user_id', 'visit_date']);
            $table->index(['restaurant_id', 'is_approved']);
            $table->index(['user_id', 'is_anonymous']);
            $table->index('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_reviews');
    }
};
