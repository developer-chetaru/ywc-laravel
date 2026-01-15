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
        Schema::create('document_expiry_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->enum('reminder_type', [
                '6_months',
                '3_months',
                '1_month',
                '2_weeks',
                '1_week',
                'expired',
                'post_expiry_weekly'
            ]);
            $table->timestamp('sent_at');
            $table->timestamp('expiry_date')->nullable(); // Snapshot of expiry date when reminder sent
            $table->text('email_content')->nullable(); // Store email content for reference
            $table->timestamps();

            // Index for efficient queries
            $table->index(['document_id', 'reminder_type', 'sent_at'], 'doc_exp_rem_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_expiry_reminders');
    }
};
