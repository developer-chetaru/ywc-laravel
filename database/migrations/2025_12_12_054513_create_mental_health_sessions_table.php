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
        Schema::create('mental_health_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('mental_health_session_bookings')->onDelete('cascade');
            $table->string('session_type'); // video, voice, chat, email
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled, no_show
            $table->text('session_notes')->nullable(); // Therapist's private notes
            $table->text('client_summary')->nullable(); // Optional summary for client
            $table->json('homework_assigned')->nullable();
            $table->json('topics_discussed')->nullable();
            $table->boolean('is_recorded')->default(false);
            $table->string('recording_path')->nullable();
            $table->dateTime('recording_deleted_at')->nullable(); // Auto-delete after 90 days
            $table->text('client_feedback')->nullable();
            $table->integer('client_rating')->nullable(); // 1-5 stars
            $table->text('therapist_feedback')->nullable();
            $table->json('chat_messages')->nullable(); // For chat/email sessions
            $table->string('video_room_id')->nullable(); // For video sessions
            $table->string('voice_call_id')->nullable(); // For voice sessions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_sessions');
    }
};
