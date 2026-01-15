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
        Schema::create('share_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('shareable'); // Polymorphic: document_share_id or profile_share_id
            $table->string('share_type'); // 'document' or 'profile'
            $table->string('action'); // 'created', 'accessed', 'downloaded', 'revoked', 'expired', 'abuse_reported'
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->text('details')->nullable(); // JSON or text for additional info
            $table->timestamps();

            // Indexes
            $table->index(['shareable_type', 'shareable_id']);
            $table->index('share_type');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_audit_logs');
    }
};
