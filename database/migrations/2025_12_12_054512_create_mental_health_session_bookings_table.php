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
        Schema::create('mental_health_session_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('therapist_id')->constrained('mental_health_therapists')->onDelete('cascade');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->string('session_type'); // video, voice, chat, email
            $table->integer('duration_minutes'); // 30, 60, 90
            $table->dateTime('scheduled_at');
            $table->string('timezone');
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed, no_show
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable(); // weekly, bi_weekly, monthly
            $table->date('recurring_end_date')->nullable();
            $table->integer('recurring_session_count')->nullable();
            $table->foreignId('recurring_series_id')->nullable()->constrained('mental_health_session_bookings')->onDelete('cascade');
            $table->boolean('requires_approval')->default(false);
            $table->string('request_status')->nullable(); // pending, approved, rejected
            $table->dateTime('request_expires_at')->nullable();
            $table->decimal('session_cost', 10, 2);
            $table->decimal('credits_used', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->text('user_notes')->nullable();
            $table->text('therapist_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_session_bookings');
    }
};
