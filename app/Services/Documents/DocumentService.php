<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Jobs\ProcessDocumentOcr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentService
{
    /**
     * Upload a document with all metadata
     */
    public function uploadDocument(
        User $user,
        UploadedFile $file,
        int $documentTypeId,
        string $documentName,
        ?string $documentNumber = null,
        ?string $issuingAuthority = null,
        ?string $issuingCountry = null,
        ?string $issueDate = null,
        ?string $expiryDate = null,
        ?string $notes = null,
        array $tags = [],
        bool $featuredOnProfile = false
    ): Document {
        // Get document type
        $documentType = DocumentType::findOrFail($documentTypeId);

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        
        // Organize by user_id/year/month
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $path = "documents/{$user->id}/{$year}/{$month}/{$filename}";

        // Store file
        $storedPath = $file->storeAs(
            "documents/{$user->id}/{$year}/{$month}",
            $filename,
            'public'
        );

        // Map document type to legacy enum for backward compatibility
        $legacyType = $this->mapDocumentTypeToLegacyEnum($documentType->slug);

        // Create document record
        $document = Document::create([
            'user_id' => $user->id,
            'type' => $legacyType, // Legacy enum for backward compatibility
            'document_type_id' => $documentTypeId,
            'document_name' => $documentName,
            'document_number' => $documentNumber,
            'issuing_authority' => $issuingAuthority,
            'issuing_country' => $issuingCountry,
            'issue_date' => $issueDate ? Carbon::parse($issueDate) : null,
            'expiry_date' => $expiryDate ? Carbon::parse($expiryDate) : null,
            'notes' => $notes,
            'tags' => $tags,
            'featured_on_profile' => $featuredOnProfile,
            'file_path' => $storedPath,
            'file_type' => $extension,
            'file_size' => (int) ceil($file->getSize() / 1024), // Size in KB
            'status' => 'pending', // Default status
            'uploaded_by' => $user->id,
            'ocr_status' => 'pending', // OCR will be processed in background
        ]);

        // Queue OCR processing
        ProcessDocumentOcr::dispatch($document);

        return $document;
    }

    /**
     * Update an existing document
     */
    public function updateDocument(
        Document $document,
        ?UploadedFile $file = null,
        ?int $documentTypeId = null,
        ?string $documentName = null,
        ?string $documentNumber = null,
        ?string $issuingAuthority = null,
        ?string $issuingCountry = null,
        ?string $issueDate = null,
        ?string $expiryDate = null,
        ?string $notes = null,
        ?array $tags = null,
        ?bool $featuredOnProfile = null,
        ?int $updatedBy = null
    ): Document {
        // Update document type if provided
        if ($documentTypeId) {
            $documentType = DocumentType::findOrFail($documentTypeId);
            $document->document_type_id = $documentTypeId;
            $document->type = $this->mapDocumentTypeToLegacyEnum($documentType->slug);
        }

        // Handle file replacement
        if ($file) {
            // Delete old file
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Delete old thumbnail
            if ($document->thumbnail_path && Storage::disk('public')->exists($document->thumbnail_path)) {
                Storage::disk('public')->delete($document->thumbnail_path);
                $document->thumbnail_path = null;
            }
            
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            
            // Organize by user_id/year/month
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            
            // Store new file
            $storedPath = $file->storeAs(
                "documents/{$document->user_id}/{$year}/{$month}",
                $filename,
                'public'
            );
            
            $document->file_path = $storedPath;
            $document->file_type = $extension;
            $document->file_size = (int) ceil($file->getSize() / 1024);
            
            // Reset status to pending when file is replaced
            $document->status = 'pending';
            
            // Reset OCR status and queue new OCR processing
            $document->ocr_status = 'pending';
            $document->ocr_data = null;
            $document->ocr_confidence = null;
            $document->ocr_error = null;
            
            // Queue OCR processing for new file
            ProcessDocumentOcr::dispatch($document);
        }

        // If document was rejected, reset status to pending when re-submitting (even without new file)
        if ($document->status === 'rejected') {
            $document->status = 'pending';
        }

        // Update metadata
        if ($documentName !== null) {
            $document->document_name = $documentName;
        }
        if ($documentNumber !== null) {
            $document->document_number = $documentNumber;
        }
        if ($issuingAuthority !== null) {
            $document->issuing_authority = $issuingAuthority;
        }
        if ($issuingCountry !== null) {
            $document->issuing_country = $issuingCountry;
        }
        if ($issueDate !== null) {
            $document->issue_date = $issueDate ? Carbon::parse($issueDate) : null;
        }
        if ($expiryDate !== null) {
            $document->expiry_date = $expiryDate ? Carbon::parse($expiryDate) : null;
        }
        if ($notes !== null) {
            $document->notes = $notes;
        }
        if ($tags !== null) {
            $document->tags = $tags;
        }
        if ($featuredOnProfile !== null) {
            $document->featured_on_profile = $featuredOnProfile;
        }
        if ($updatedBy) {
            $document->updated_by = $updatedBy;
        }

        $document->save();

        return $document;
    }

    /**
     * Map new document type slug to legacy enum value
     * This ensures backward compatibility with existing code
     */
    protected function mapDocumentTypeToLegacyEnum(string $slug): string
    {
        $mapping = [
            'passport' => 'passport',
            'certificates' => 'certificate',
            'ids-visas' => 'idvisa',
            'references' => 'other',
            'contracts' => 'other',
            'payslips' => 'other',
            'insurance' => 'other',
            'travel-documents' => 'other',
            'other' => 'other',
        ];

        return $mapping[$slug] ?? 'other';
    }
}
