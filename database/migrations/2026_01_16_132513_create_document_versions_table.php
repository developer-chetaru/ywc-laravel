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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('file_path')->nullable(); // Path to versioned file
            $table->string('file_type', 20)->nullable(); // pdf, jpg, png
            $table->integer('file_size')->nullable(); // in KB
            $table->string('thumbnail_path')->nullable(); // Thumbnail for this version
            
            // Store document metadata at time of version
            $table->json('metadata')->nullable(); // Store document_name, document_number, etc.
            $table->text('change_notes')->nullable(); // User notes about what changed
            
            // Who created this version
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // OCR data snapshot
            $table->json('ocr_data')->nullable();
            $table->string('ocr_status')->nullable();
            $table->float('ocr_confidence')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['document_id', 'version_number']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
