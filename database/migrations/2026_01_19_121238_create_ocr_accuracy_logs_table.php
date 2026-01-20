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
        Schema::create('ocr_accuracy_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Original OCR Data
            $table->json('original_ocr_data')->nullable();
            
            // Corrected Data
            $table->json('corrected_data')->nullable();
            
            // Field-level accuracy
            $table->json('field_accuracy')->nullable()->comment('Accuracy per field');
            
            // Overall metrics
            $table->decimal('overall_accuracy', 5, 2)->nullable()->comment('0-100 percentage');
            $table->integer('fields_correct')->default(0);
            $table->integer('fields_total')->default(0);
            $table->integer('characters_correct')->default(0);
            $table->integer('characters_total')->default(0);
            
            // OCR Engine Info
            $table->string('ocr_engine')->nullable()->comment('tesseract, google_vision, etc');
            $table->string('ocr_version')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_language')->nullable();
            
            // Quality Metrics
            $table->integer('confidence_score')->nullable()->comment('0-100');
            $table->boolean('manual_correction_required')->default(false);
            $table->text('correction_notes')->nullable();
            
            // Timestamps
            $table->timestamp('corrected_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['document_id', 'created_at']);
            $table->index('overall_accuracy');
            $table->index('ocr_engine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocr_accuracy_logs');
    }
};
