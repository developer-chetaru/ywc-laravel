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
        Schema::create('mental_health_support_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('focus_area'); // anxiety, depression, trauma, etc.
            $table->foreignId('facilitator_id')->nullable()->constrained('mental_health_therapists')->onDelete('set null');
            $table->integer('max_participants')->default(12);
            $table->integer('current_participants')->default(0);
            $table->string('schedule_frequency'); // weekly, bi_weekly, monthly
            $table->string('day_of_week')->nullable();
            $table->time('meeting_time')->nullable();
            $table->string('meeting_type'); // video, voice
            $table->string('status')->default('active'); // active, full, completed, cancelled
            $table->dateTime('next_meeting_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_support_groups');
    }
};
