<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\TemporaryUploadedFile;
use App\Models\Document;
use App\Models\DocumentType;
use App\Services\Documents\DocumentService;
use App\Services\Documents\ThumbnailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app-laravel')]
class DocumentUpload extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $scanning = false;
    public $scanResult = null;
    public $scanError = null;
    public $editingDocumentId = null; // For edit mode
    public $existingFile = null; // Current file path when editing
    public $rejectionNotes = null; // Rejection notes for rejected documents

    // Form fields
    public $document_type_id = null;
    public $document_name = null;
    public $document_number = null;
    public $issuing_authority = null;
    public $issuing_country = null;
    public $issue_date = null;
    public $expiry_date = null;
    public $notes = null;
    public $tags = [];
    public $tagInput = '';
    public $featured_on_profile = false;
    public $file = null;
    public $filePreview = null;
    public $uploadProgress = 0;
    public $isUploading = false;

    public function mount()
    {
        // Component initialization
    }

    #[On('openUploadModal')]
    public function openModal(?int $documentId = null, string $mode = 'add')
    {
        $this->editingDocumentId = $documentId;
        $this->resetForm();
        
        if ($mode === 'edit' && $documentId) {
            $document = Auth::user()->documents()
                ->with(['documentType', 'statusChanges.changedBy'])
                ->findOrFail($documentId);
            
            $this->document_type_id = $document->document_type_id;
            $this->document_name = $document->document_name;
            $this->document_number = $document->document_number;
            $this->issuing_authority = $document->issuing_authority;
            $this->issuing_country = $document->issuing_country;
            $this->issue_date = $document->issue_date?->format('Y-m-d');
            $this->expiry_date = $document->expiry_date?->format('Y-m-d');
            $this->notes = $document->notes;
            $this->tags = $document->tags ?? [];
            $this->featured_on_profile = $document->featured_on_profile;
            $this->existingFile = $document->file_path;
            
            // Get latest rejection notes if document is rejected
            if ($document->status === 'rejected') {
                $latestRejection = $document->statusChanges()
                    ->where('new_status', 'rejected')
                    ->latest()
                    ->first();
                $this->rejectionNotes = $latestRejection?->notes;
            } else {
                $this->rejectionNotes = null;
            }
            
            if ($document->file_path) {
                $this->filePreview = asset('storage/' . $document->file_path);
            }
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->editingDocumentId = null;
        $this->existingFile = null;
        $this->document_type_id = null;
        $this->document_name = null;
        $this->document_number = null;
        $this->issuing_authority = null;
        $this->issuing_country = null;
        $this->issue_date = null;
        $this->expiry_date = null;
        $this->notes = null;
        $this->tags = [];
        $this->tagInput = '';
        $this->featured_on_profile = false;
        $this->file = null;
        $this->filePreview = null;
        $this->scanResult = null;
        $this->scanError = null;
        $this->rejectionNotes = null;
        $this->resetValidation();
    }

    public function updatedFile()
    {
        if ($this->file) {
            // Auto-populate document name from filename
            if (!$this->document_name) {
                $this->document_name = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
            }
            
            // Generate preview for images
            if (in_array($this->file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                $this->filePreview = $this->file->temporaryUrl();
            }
        }
    }

    public function updatedDocumentTypeId()
    {
        // Reset conditional fields when document type changes
        $this->document_number = null;
        $this->issuing_authority = null;
        $this->expiry_date = null;
    }

    public function addTag()
    {
        if ($this->tagInput && !in_array($this->tagInput, $this->tags)) {
            $this->tags[] = trim($this->tagInput);
            $this->tagInput = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function scan()
    {
        if (!$this->file) {
            $this->addError('file', 'Please upload a file first.');
            return;
        }

        $this->scanning = true;
        $this->scanError = null;
        $this->scanResult = null;

        try {
            // Store file temporarily for scanning
            $tempPath = $this->file->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);

            // Create a request object for the scan method
            $request = new \Illuminate\Http\Request();
            $request->files->set('docFile', new \Illuminate\Http\UploadedFile(
                $fullPath,
                $this->file->getClientOriginalName(),
                $this->file->getMimeType(),
                null,
                true
            ));

            $controller = app(\App\Http\Controllers\CareerHistoryController::class);
            $response = $controller->scan($request);

            $data = json_decode($response->getContent(), true);

            if ($data['success'] ?? false) {
                $this->scanResult = $data['text'] ?? '';
                
                // Try to auto-fill fields from OCR text
                $this->autoFillFromScan($data['text']);
            } else {
                $this->scanError = $data['message'] ?? 'Scan failed. Please try again.';
            }

            // Clean up temp file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        } catch (\Exception $e) {
            Log::error('Document scan error: ' . $e->getMessage());
            $this->scanError = 'Scanning failed: ' . $e->getMessage();
        } finally {
            $this->scanning = false;
        }
    }

    protected function autoFillFromScan($text)
    {
        // Basic auto-fill logic - can be enhanced
        $text = strtolower($text);
        
        // Try to extract document number (common patterns)
        if (preg_match('/\b([A-Z0-9]{6,20})\b/i', $text, $matches)) {
            if (!$this->document_number) {
                $this->document_number = strtoupper($matches[1]);
            }
        }
        
        // Try to extract dates
        if (preg_match('/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/', $text, $matches)) {
            $dateStr = $matches[1];
            $parsed = \Carbon\Carbon::createFromFormat('d/m/Y', str_replace('-', '/', $dateStr));
            if ($parsed && !$this->issue_date) {
                $this->issue_date = $parsed->format('Y-m-d');
            }
        }
    }

    public function save()
    {
        $documentType = DocumentType::find($this->document_type_id);
        
        if (!$documentType) {
            $this->addError('document_type_id', 'Please select a document type.');
            return;
        }

        $rules = [
            'document_type_id' => 'required|exists:document_types,id',
            'document_name' => 'required|string|max:255',
            'file' => $this->editingDocumentId ? 'nullable|file|mimes:pdf,jpg,jpeg,png,heic|max:10240' : 'required|file|mimes:pdf,jpg,jpeg,png,heic|max:10240', // 10MB
        ];

        // Conditional validation based on document type requirements
        if ($documentType->requires_document_number) {
            $rules['document_number'] = 'required|string|max:255';
        }

        if ($documentType->requires_issuing_authority) {
            $rules['issuing_authority'] = 'required|string|max:255';
        }

        if ($documentType->requires_expiry_date) {
            $rules['expiry_date'] = 'required|date|after:issue_date';
        }

        if ($this->issue_date) {
            $rules['issue_date'] = 'date|before_or_equal:today';
        }

        if ($this->expiry_date && $this->issue_date) {
            $rules['expiry_date'] = 'date|after:issue_date';
        }

        $validated = $this->validate($rules);

        try {
            $documentService = app(DocumentService::class);
            $thumbnailService = app(ThumbnailService::class);
            
            if ($this->editingDocumentId) {
                // Update existing document
                $document = Document::where('user_id', Auth::id())->findOrFail($this->editingDocumentId);
                
                $document = $documentService->updateDocument(
                    document: $document,
                    file: $this->file, // Can be null if not replacing file
                    documentTypeId: $this->document_type_id,
                    documentName: $this->document_name,
                    documentNumber: $this->document_number,
                    issuingAuthority: $this->issuing_authority,
                    issuingCountry: $this->issuing_country,
                    issueDate: $this->issue_date,
                    expiryDate: $this->expiry_date,
                    notes: $this->notes,
                    tags: $this->tags,
                    featuredOnProfile: $this->featured_on_profile,
                    updatedBy: Auth::id()
                );
                
                // Generate thumbnail if file was replaced
                if ($this->file) {
                    try {
                        $thumbnailService->ensureThumbnail($document);
                    } catch (\Exception $e) {
                        Log::warning('Thumbnail generation failed: ' . $e->getMessage());
                    }
                }
                
                session()->flash('message', 'Document updated successfully!');
            } else {
                // Create new document
                $document = $documentService->uploadDocument(
                    user: Auth::user(),
                    file: $this->file,
                    documentTypeId: $this->document_type_id,
                    documentName: $this->document_name,
                    documentNumber: $this->document_number,
                    issuingAuthority: $this->issuing_authority,
                    issuingCountry: $this->issuing_country,
                    issueDate: $this->issue_date,
                    expiryDate: $this->expiry_date,
                    notes: $this->notes,
                    tags: $this->tags,
                    featuredOnProfile: $this->featured_on_profile
                );

                // Generate thumbnail
                try {
                    $thumbnailService->ensureThumbnail($document);
                } catch (\Exception $e) {
                    Log::warning('Thumbnail generation failed: ' . $e->getMessage());
                }
                
                session()->flash('message', 'Document uploaded successfully!');
            }
            
            $this->closeModal();
            
            // Dispatch event to refresh document list
            $this->dispatch('documentUploaded');
            
        } catch (\Exception $e) {
            Log::error('Document save failed: ' . $e->getMessage());
            $this->addError('file', 'Failed to save document: ' . $e->getMessage());
        }
    }

    public function getDocumentTypesProperty()
    {
        return DocumentType::active()->ordered()->get();
    }

    public function getSelectedDocumentTypeProperty()
    {
        if (!$this->document_type_id) {
            return null;
        }
        return DocumentType::find($this->document_type_id);
    }

    public function render()
    {
        return view('livewire.documents.document-upload', [
            'documentTypes' => $this->documentTypes,
            'selectedDocumentType' => $this->selectedDocumentType,
        ]);
    }
}
