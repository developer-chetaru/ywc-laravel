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
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('verification_level_id')->constrained('verification_levels')->onDelete('cascade');
            $table->foreignId('verifier_id')->nullable()->constrained('users')->onDelete('set null'); // User who verified
            $table->string('verifier_type')->nullable(); // 'user', 'employer', 'training_provider', 'official'
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->text('notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['document_id', 'verification_level_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};
