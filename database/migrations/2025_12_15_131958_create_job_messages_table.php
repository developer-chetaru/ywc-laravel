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
        Schema::create('job_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('temporary_work_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            
            // Message content
            $table->text('message');
            $table->string('subject')->nullable();
            $table->json('attachments')->nullable(); // File paths
            
            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            
            // Type
            $table->enum('message_type', ['general', 'interview_request', 'interview_confirmation', 'offer', 'follow_up', 'question'])->default('general');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['job_post_id', 'sender_id', 'receiver_id']);
            $table->index(['job_application_id', 'sender_id', 'receiver_id']);
            $table->index(['receiver_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_messages');
    }
};
