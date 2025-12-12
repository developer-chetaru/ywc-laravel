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
        Schema::create('mental_health_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // anxiety, depression, stress, relationships, etc.
            $table->string('resource_type'); // article, video, audio, worksheet, pdf
            $table->text('content')->nullable(); // For articles/text content
            $table->string('file_path', 2048)->nullable(); // For videos, audio, PDFs
            $table->string('thumbnail_path', 2048)->nullable();
            $table->json('tags')->nullable();
            $table->json('target_audience')->nullable(); // all_crew, specific_positions
            $table->integer('reading_time_minutes')->nullable();
            $table->string('difficulty_level')->nullable(); // beginner, intermediate, advanced
            $table->json('related_resources')->nullable(); // IDs of related resources
            $table->string('author')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('bookmark_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_resources');
    }
};
