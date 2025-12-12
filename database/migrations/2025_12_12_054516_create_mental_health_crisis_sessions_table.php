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
        Schema::create('mental_health_crisis_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained('mental_health_therapists')->onDelete('set null');
            $table->string('severity_level'); // low, medium, high, critical
            $table->json('assessment_answers')->nullable();
            $table->string('location')->nullable();
            $table->string('session_type'); // video, voice, chat
            $table->dateTime('connected_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('safety_plan_created')->nullable();
            $table->boolean('emergency_services_contacted')->default(false);
            $table->text('emergency_services_details')->nullable();
            $table->boolean('captain_notified')->default(false);
            $table->text('follow_up_notes')->nullable();
            $table->dateTime('follow_up_scheduled_at')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, escalated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_crisis_sessions');
    }
};
