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
        Schema::create('forum_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reportable_type', 50); // 'thread', 'post'
            $table->unsignedInteger('reportable_id');
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 50); // 'spam', 'harassment', 'off_topic', 'inappropriate', 'libel', 'privacy'
            $table->text('explanation');
            $table->string('status', 50)->default('pending'); // 'pending', 'resolved', 'dismissed'
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('moderator_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['reportable_type', 'reportable_id']);
            $table->index('reporter_id');
            $table->index('status');
            $table->index('moderator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_reports');
    }
};
