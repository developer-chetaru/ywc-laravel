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
        Schema::create('itinerary_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_source_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable();
            $table->string('region')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('season')->nullable();
            $table->unsignedInteger('duration_days')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('distance_nm', 8, 2)->default(0);
            $table->unsignedInteger('average_leg_nm')->default(0);
            $table->enum('visibility', ['public', 'private', 'crew'])->default('private');
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->boolean('is_template')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('copies_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->json('analytics_snapshot')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['visibility', 'region']);
            $table->index('is_template');
            $table->foreign('template_source_id')
                ->references('id')
                ->on('itinerary_routes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_routes');
    }
};

