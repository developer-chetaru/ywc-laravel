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
        Schema::create('verification_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_id')->constrained('document_verifications')->onDelete('cascade');
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('User who filed appeal');
            
            // Appeal Details
            $table->text('reason')->comment('Reason for appeal');
            $table->json('disputed_fields')->nullable()->comment('Which fields/decisions are disputed');
            $table->text('supporting_evidence')->nullable();
            $table->json('evidence_files')->nullable()->comment('Paths to uploaded evidence files');
            
            // Status & Assignment
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('priority')->default(3)->comment('1=High, 2=Medium, 3=Low');
            
            // Review Process
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Original vs New Decision
            $table->string('original_decision')->nullable();
            $table->string('new_decision')->nullable();
            $table->json('changes_made')->nullable();
            
            // Communication
            $table->boolean('user_notified')->default(false);
            $table->timestamp('user_notified_at')->nullable();
            
            // Metadata
            $table->string('appeal_reference')->unique();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index('assigned_to');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_appeals');
    }
};
