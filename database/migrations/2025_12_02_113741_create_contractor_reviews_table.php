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
        Schema::create('contractor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('review');
            $table->string('service_type')->nullable();
            $table->decimal('service_cost', 10, 2)->nullable();
            $table->string('timeframe')->nullable();
            $table->tinyInteger('overall_rating')->unsigned();
            $table->tinyInteger('quality_rating')->unsigned()->nullable();
            $table->tinyInteger('professionalism_rating')->unsigned()->nullable();
            $table->tinyInteger('pricing_rating')->unsigned()->nullable();
            $table->tinyInteger('timeliness_rating')->unsigned()->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->boolean('would_hire_again')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('service_date')->nullable();
            $table->string('yacht_name')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['contractor_id', 'user_id', 'service_date']);
            $table->index(['contractor_id', 'is_approved']);
            $table->index(['user_id', 'is_anonymous']);
            $table->index('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_reviews');
    }
};
