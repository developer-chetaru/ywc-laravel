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
        Schema::create('issued_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('training_providers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('certification_id')->nullable()->constrained('training_certifications')->onDelete('set null');
            $table->string('certificate_number')->unique();
            $table->string('certificate_name');
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('certificate_file_path')->nullable()->comment('Path to PDF certificate');
            $table->enum('status', ['active', 'expired', 'revoked', 'suspended'])->default('active');
            $table->text('revocation_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('verification_data')->nullable()->comment('QR code data, verification URL, etc');
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['provider_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('certificate_number');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issued_certificates');
    }
};
