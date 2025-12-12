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
        Schema::create('mental_health_wellness_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reminder_type'); // medication, exercise, journaling, mood_tracking, etc.
            $table->string('title');
            $table->text('message')->nullable();
            $table->time('reminder_time');
            $table->json('reminder_days')->nullable(); // [1,2,3,4,5] for weekdays
            $table->boolean('is_active')->default(true);
            $table->json('channels')->nullable(); // ['push', 'email', 'sms']
            $table->integer('snooze_minutes')->default(5);
            $table->json('quiet_hours')->nullable(); // {start: '22:00', end: '07:00'}
            $table->dateTime('last_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_wellness_reminders');
    }
};
