<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class VersionService
{
    /**
     * Create a new version of a document
     * 
     * @param Document $document
     * @param string|null $changeNotes
     * @param int|null $userId
     * @return DocumentVersion
     */
    public function createVersion(Document $document, ?string $changeNotes = null, ?int $userId = null): DocumentVersion
    {
        // Get next version number
        $nextVersion = $this->getNextVersionNumber($document);
        
        // Copy current file to version storage
        $versionedFilePath = null;
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $versionedFilePath = $this->copyFileForVersion($document, $nextVersion);
        }
        
        // Copy thumbnail if exists
        $versionedThumbnailPath = null;
        if ($document->thumbnail_path && Storage::disk('public')->exists($document->thumbnail_path)) {
            $versionedThumbnailPath = $this->copyThumbnailForVersion($document, $nextVersion);
        }
        
        // Store metadata snapshot
        $metadata = [
            'document_name' => $document->document_name,
            'document_number' => $document->document_number,
            'issuing_authority' => $document->issuing_authority,
            'issuing_country' => $document->issuing_country,
            'issue_date' => $document->issue_date?->toDateString(),
            'expiry_date' => $document->expiry_date?->toDateString(),
            'dob' => $document->dob?->toDateString(),
            'notes' => $document->notes,
            'tags' => $document->tags,
        ];
        
        // Create version record
        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $nextVersion,
            'file_path' => $versionedFilePath,
            'file_type' => $document->file_type,
            'file_size' => $document->file_size,
            'thumbnail_path' => $versionedThumbnailPath,
            'metadata' => $metadata,
            'change_notes' => $changeNotes,
            'created_by' => $userId ?? auth()->id(),
            'ocr_data' => $document->ocr_data,
            'ocr_status' => $document->ocr_status,
            'ocr_confidence' => $document->ocr_confidence,
        ]);
        
        // Update document version number
        $document->update(['version' => $nextVersion]);
        
        return $version;
    }

    /**
     * Restore a document to a specific version
     * 
     * @param Document $document
     * @param DocumentVersion $version
     * @param int|null $userId
     * @return Document
     */
    public function restoreVersion(Document $document, DocumentVersion $version, ?int $userId = null): Document
    {
        // Create a new version of current state before restoring
        $this->createVersion($document, "Restored from version {$version->version_number}", $userId);
        
        // Restore file
        if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
            // Copy versioned file back to document location
            $restoredPath = $this->copyVersionFileToDocument($version, $document);
            $document->file_path = $restoredPath;
        }
        
        // Restore thumbnail
        if ($version->thumbnail_path && Storage::disk('public')->exists($version->thumbnail_path)) {
            $restoredThumbnailPath = $this->copyVersionThumbnailToDocument($version, $document);
            $document->thumbnail_path = $restoredThumbnailPath;
        }
        
        // Restore metadata
        if ($version->metadata) {
            $document->document_name = $version->metadata['document_name'] ?? $document->document_name;
            $document->document_number = $version->metadata['document_number'] ?? $document->document_number;
            $document->issuing_authority = $version->metadata['issuing_authority'] ?? $document->issuing_authority;
            $document->issuing_country = $version->metadata['issuing_country'] ?? $document->issuing_country;
            $document->issue_date = $version->metadata['issue_date'] ?? $document->issue_date;
            $document->expiry_date = $version->metadata['expiry_date'] ?? $document->expiry_date;
            $document->dob = $version->metadata['dob'] ?? $document->dob;
            $document->notes = $version->metadata['notes'] ?? $document->notes;
            $document->tags = $version->metadata['tags'] ?? $document->tags;
        }
        
        // Restore OCR data
        $document->ocr_data = $version->ocr_data;
        $document->ocr_status = $version->ocr_status;
        $document->ocr_confidence = $version->ocr_confidence;
        
        // Update version number
        $nextVersion = $this->getNextVersionNumber($document);
        $document->version = $nextVersion;
        $document->updated_by = $userId ?? auth()->id();
        
        $document->save();
        
        return $document;
    }

    /**
     * Get next version number for a document
     */
    protected function getNextVersionNumber(Document $document): int
    {
        $latestVersion = DocumentVersion::where('document_id', $document->id)
            ->max('version_number');
        
        return max($document->version, $latestVersion ?? 0) + 1;
    }

    /**
     * Copy file to version storage
     */
    protected function copyFileForVersion(Document $document, int $versionNumber): string
    {
        $sourcePath = Storage::disk('public')->path($document->file_path);
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $versionDir = "document-versions/{$document->id}";
        $versionFileName = "v{$versionNumber}." . $extension;
        $versionPath = "{$versionDir}/{$versionFileName}";
        
        // Ensure directory exists
        Storage::disk('public')->makeDirectory($versionDir);
        
        // Copy file
        Storage::disk('public')->put($versionPath, File::get($sourcePath));
        
        return $versionPath;
    }

    /**
     * Copy thumbnail to version storage
     */
    protected function copyThumbnailForVersion(Document $document, int $versionNumber): string
    {
        $sourcePath = Storage::disk('public')->path($document->thumbnail_path);
        $extension = pathinfo($document->thumbnail_path, PATHINFO_EXTENSION);
        $versionDir = "document-versions/{$document->id}/thumbnails";
        $versionFileName = "v{$versionNumber}." . $extension;
        $versionPath = "{$versionDir}/{$versionFileName}";
        
        // Ensure directory exists
        Storage::disk('public')->makeDirectory($versionDir);
        
        // Copy file
        Storage::disk('public')->put($versionPath, File::get($sourcePath));
        
        return $versionPath;
    }

    /**
     * Copy version file back to document location
     */
    protected function copyVersionFileToDocument(DocumentVersion $version, Document $document): string
    {
        $sourcePath = Storage::disk('public')->path($version->file_path);
        $extension = pathinfo($version->file_path, PATHINFO_EXTENSION);
        $documentDir = "documents";
        $documentFileName = "doc_{$document->id}_" . time() . "." . $extension;
        $documentPath = "{$documentDir}/{$documentFileName}";
        
        // Copy file
        Storage::disk('public')->put($documentPath, File::get($sourcePath));
        
        return $documentPath;
    }

    /**
     * Copy version thumbnail back to document location
     */
    protected function copyVersionThumbnailToDocument(DocumentVersion $version, Document $document): string
    {
        $sourcePath = Storage::disk('public')->path($version->thumbnail_path);
        $extension = pathinfo($version->thumbnail_path, PATHINFO_EXTENSION);
        $thumbnailDir = "thumbnails";
        $thumbnailFileName = "{$document->id}_" . time() . "." . $extension;
        $thumbnailPath = "{$thumbnailDir}/{$thumbnailFileName}";
        
        // Ensure directory exists
        Storage::disk('public')->makeDirectory($thumbnailDir);
        
        // Copy file
        Storage::disk('public')->put($thumbnailPath, File::get($sourcePath));
        
        return $thumbnailPath;
    }

    /**
     * Delete old versions (keep only last N versions)
     */
    public function cleanupOldVersions(Document $document, int $keepCount = 10): void
    {
        $versions = DocumentVersion::where('document_id', $document->id)
            ->orderBy('version_number', 'desc')
            ->skip($keepCount)
            ->get();
        
        foreach ($versions as $version) {
            // Delete files
            if ($version->file_path) {
                Storage::disk('public')->delete($version->file_path);
            }
            if ($version->thumbnail_path) {
                Storage::disk('public')->delete($version->thumbnail_path);
            }
            
            // Delete version record
            $version->delete();
        }
    }
}
