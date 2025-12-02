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
        Schema::create('review_flags', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // Polymorphic relation: yacht_review, marina_review, contractor_review, etc.
            $table->foreignId('review_id'); // ID of the specific review
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // User who flagged
            $table->enum('reason', [
                'false_information',
                'defamatory_content',
                'personal_attack',
                'breach_of_confidentiality',
                'duplicate_spam',
                'violation_of_guidelines',
                'other'
            ]);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'under_review', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index(['reviewable_type', 'review_id']);
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_flags');
    }
};
