<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\Documents\OcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $document;
    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     */
    public function handle(OcrService $ocrService): void
    {
        try {
            // Reload document to ensure we have latest data
            $this->document->refresh();

            // Update status to processing
            $this->document->update([
                'ocr_status' => 'processing'
            ]);

            // Get document type (use legacy type for now)
            $documentType = $this->document->type ?? 'other';

            // Process OCR
            $result = $ocrService->detectDocumentStructure(
                $this->document->file_path,
                $documentType
            );

            if ($result['success']) {
                // Store OCR results
                $ocrData = [
                    'text' => $result['text'],
                    'fields' => $result['fields'] ?? [],
                    'confidence' => $result['confidence'] ?? 0,
                    'overall_confidence' => $result['overall_confidence'] ?? 0,
                    'processed_at' => now()->toDateTimeString()
                ];

                $this->document->update([
                    'ocr_data' => $ocrData,
                    'ocr_confidence' => $result['overall_confidence'] ?? 0,
                    'ocr_status' => 'completed',
                ]);

                Log::info("OCR completed for document {$this->document->id}", [
                    'confidence' => $result['overall_confidence'],
                    'fields_extracted' => count($result['fields'] ?? [])
                ]);
            } else {
                // OCR failed
                $this->document->update([
                    'ocr_status' => 'failed',
                    'ocr_error' => $result['message'] ?? 'OCR processing failed'
                ]);

                Log::warning("OCR failed for document {$this->document->id}", [
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error("OCR job failed for document {$this->document->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->document->update([
                'ocr_status' => 'failed',
                'ocr_error' => $e->getMessage()
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }
}
