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
        Schema::create('mental_health_course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('mental_health_courses')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->string('lesson_type'); // text, video, audio, quiz, exercise
            $table->text('content')->nullable(); // For text lessons
            $table->string('video_path', 2048)->nullable();
            $table->string('audio_path', 2048)->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('quiz_data')->nullable(); // For quiz lessons
            $table->json('exercise_data')->nullable(); // For interactive exercises
            $table->boolean('has_subtitles')->default(false);
            $table->json('subtitle_languages')->nullable();
            $table->string('transcript_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_course_lessons');
    }
};
