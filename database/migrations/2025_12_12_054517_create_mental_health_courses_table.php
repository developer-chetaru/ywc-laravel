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
        Schema::create('mental_health_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // mental_health_literacy, self_care, etc.
            $table->json('modules')->nullable(); // Array of module/lesson structure
            $table->integer('total_duration_minutes')->nullable();
            $table->string('difficulty_level')->nullable(); // beginner, intermediate, advanced
            $table->json('prerequisites')->nullable(); // Course IDs
            $table->boolean('certificate_available')->default(false);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('enrollment_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_courses');
    }
};
