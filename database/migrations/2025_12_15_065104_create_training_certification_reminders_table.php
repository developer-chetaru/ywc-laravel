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
        Schema::create('training_certification_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_certification_id')->constrained('training_user_certifications')->onDelete('cascade');
            $table->enum('reminder_type', ['6_months', '3_months', '1_month', 'expired']); // Months before expiry
            $table->date('reminder_date');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->text('email_content')->nullable(); // Store email content if needed
            $table->json('course_recommendations')->nullable(); // Suggested courses for renewal
            $table->timestamps();
            
            // Index for reminder queries
            $table->index(['reminder_date', 'is_sent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_certification_reminders');
    }
};
