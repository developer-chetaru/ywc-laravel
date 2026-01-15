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
        Schema::create('profile_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Profile owner
            $table->string('share_token', 64)->unique(); // Secure token for share link
            $table->string('recipient_email')->nullable(); // Optional: if shared via email
            $table->string('recipient_name')->nullable();
            $table->text('personal_message')->nullable(); // Optional message
            $table->json('shared_sections')->nullable(); // Which sections to share: ['personal_info', 'documents', 'career_history']
            $table->json('document_categories')->nullable(); // Specific document categories to include
            $table->json('career_entry_ids')->nullable(); // Specific career entries to include
            $table->timestamp('expires_at')->nullable(); // Optional expiry date
            $table->boolean('is_active')->default(true); // Can be revoked
            $table->integer('view_count')->default(0); // Track views
            $table->integer('download_count')->default(0); // Track downloads
            $table->timestamp('last_accessed_at')->nullable();
            $table->string('ip_address')->nullable(); // Track IP for analytics
            $table->string('qr_code_path')->nullable(); // Path to generated QR code image
            $table->timestamps();

            // Indexes
            $table->index('share_token');
            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_shares');
    }
};
