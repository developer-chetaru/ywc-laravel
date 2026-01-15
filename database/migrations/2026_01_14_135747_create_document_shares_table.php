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
        Schema::create('document_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Owner of the documents
            $table->string('share_token', 64)->unique(); // Secure token for share link
            $table->string('recipient_email')->nullable(); // Optional: if shared via email
            $table->string('recipient_name')->nullable();
            $table->text('personal_message')->nullable(); // Optional message from sender
            $table->timestamp('expires_at')->nullable(); // Optional expiry date
            $table->boolean('is_active')->default(true); // Can be revoked
            $table->integer('access_count')->default(0); // Track how many times accessed
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('share_token');
            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
        });

        // Pivot table for many-to-many relationship between shares and documents
        Schema::create('document_share_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_share_id')->constrained('document_shares')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['document_share_id', 'document_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_share_documents');
        Schema::dropIfExists('document_shares');
    }
};
