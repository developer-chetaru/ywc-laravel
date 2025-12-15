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
        Schema::create('training_user_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('certification_id')->constrained('training_certifications')->onDelete('cascade');
            $table->foreignId('provider_course_id')->nullable()->constrained('training_provider_courses')->onDelete('set null');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['valid', 'expiring_soon', 'expired'])->default('valid');
            $table->string('certificate_number')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->string('certificate_document_path')->nullable(); // Uploaded certificate file
            $table->text('notes')->nullable();
            $table->boolean('is_auto_tracked')->default(false); // Auto-tracked from booking
            $table->timestamps();
            
            // Index for expiry tracking
            $table->index(['user_id', 'expiry_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_user_certifications');
    }
};
