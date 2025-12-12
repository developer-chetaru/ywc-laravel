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
        Schema::create('mental_health_therapist_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('mental_health_therapists')->onDelete('cascade');
            $table->string('credential_type'); // license, education, certification, insurance, background_check, reference
            $table->string('document_path', 2048)->nullable(); // Path to uploaded document
            $table->string('document_name')->nullable();
            $table->string('status')->default('pending'); // pending, verified, expired, rejected
            $table->text('admin_notes')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable(); // Additional credential-specific data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_health_therapist_credentials');
    }
};
