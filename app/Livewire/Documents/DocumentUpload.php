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
use Livewire\Attributes\Validate;

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
    #[Validate('required|file|mimes:pdf,jpg,jpeg,png,heic|max:5120', onUpdate: false, message: ['file.required' => 'Please upload a document file. File is required.'])]
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
        
        // Explicitly ensure file is null for new documents
        if ($mode === 'add' || !$documentId) {
            $this->file = null;
            $this->filePreview = null;
        }
        
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
        // Dispatch event that file was removed
        if (!$this->editingDocumentId) {
            $this->dispatch('file-removed');
        }
    }

    public function updatedFile()
    {
        if ($this->file) {
            // Check if file is valid
            if (!$this->file->isValid()) {
                $this->addError('file', 'The uploaded file is not valid. Please try again.');
                $this->file = null;
                $this->filePreview = null;
                $this->dispatch('file-removed');
                return;
            }
            
            // Validate file immediately
            $this->validateFile();
            
            // Only proceed if validation passed
            if ($this->getErrorBag()->has('file')) {
                $this->dispatch('file-removed');
                return;
            }
            
            // Auto-populate document name from filename
            if (!$this->document_name) {
                $this->document_name = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
            }
            
            // Generate preview for images
            if (in_array($this->file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                $this->filePreview = $this->file->temporaryUrl();
            }
            
            // Dispatch event that file is ready
            $this->dispatch('file-ready');
        } else {
            // Clear preview if file is removed
            $this->filePreview = null;
            if (!$this->editingDocumentId) {
                $this->dispatch('file-removed');
            }
        }
    }

    protected function validateFile()
    {
        if (!$this->file) {
            return;
        }

        // Validate file size (5MB = 5120 KB)
        $maxSize = 5120; // 5MB in KB
        $fileSizeKB = $this->file->getSize() / 1024;
        
        if ($fileSizeKB > $maxSize) {
            $this->addError('file', 'File size must not exceed 5MB. Current file size: ' . number_format($fileSizeKB, 2) . ' KB');
            $this->file = null;
            $this->filePreview = null;
            return;
        }

        // Validate file type
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/heic'];
        $fileMime = $this->file->getMimeType();
        
        if (!in_array($fileMime, $allowedMimes)) {
            $this->addError('file', 'Invalid file type. Only PDF, JPG, PNG, and HEIC files are allowed.');
            $this->file = null;
            $this->filePreview = null;
            return;
        }

        // Validate file extension
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'heic'];
        $fileExtension = strtolower($this->file->getClientOriginalExtension());
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $this->addError('file', 'Invalid file extension. Only .pdf, .jpg, .jpeg, .png, and .heic files are allowed.');
            $this->file = null;
            $this->filePreview = null;
            return;
        }

        // Clear any previous errors if validation passes
        $this->resetErrorBag('file');
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
        // ABSOLUTE FIRST CHECK: For new documents, file is MANDATORY - NO EXCEPTIONS
        if (!$this->editingDocumentId) {
            // Check 1: Null check
            if (is_null($this->file)) {
                $this->addError('file', 'Please upload a document file. File is required.');
                $this->dispatch('validation-failed', message: 'File is required');
                session()->flash('error', 'Cannot save document without a file. Please upload a file first.');
                Log::warning('BLOCKED: Attempt to save document without file. User: ' . Auth::id());
                return;
            }
            
            // Check 2: Existence check
            if (!isset($this->file)) {
                $this->addError('file', 'Please upload a document file. File is required.');
                $this->dispatch('validation-failed', message: 'File is required');
                session()->flash('error', 'Cannot save document without a file. Please upload a file first.');
                Log::warning('BLOCKED: Attempt to save document without file (isset check). User: ' . Auth::id());
                return;
            }
            
            // Check 3: Empty check
            if (empty($this->file)) {
                $this->addError('file', 'Please upload a document file. File is required.');
                $this->dispatch('validation-failed', message: 'File is required');
                session()->flash('error', 'Cannot save document without a file. Please upload a file first.');
                Log::warning('BLOCKED: Attempt to save document with empty file. User: ' . Auth::id());
                return;
            }
            
            // Check 4: Valid file object check
            if (!($this->file instanceof \Illuminate\Http\UploadedFile || $this->file instanceof TemporaryUploadedFile)) {
                $this->addError('file', 'Invalid file object. Please upload a file again.');
                $this->dispatch('validation-failed', message: 'File is invalid');
                session()->flash('error', 'Invalid file. Please upload a file again.');
                Log::warning('BLOCKED: Attempt to save with invalid file object. User: ' . Auth::id());
                return;
            }
            
            // Check 5: File validity check
            try {
                if (!$this->file->isValid()) {
                    $this->addError('file', 'The uploaded file is not valid. Please try again.');
                    $this->dispatch('validation-failed', message: 'File is invalid');
                    Log::warning('BLOCKED: Attempt to save with invalid file. User: ' . Auth::id());
                    return;
                }
            } catch (\Exception $e) {
                $this->addError('file', 'Error validating file: ' . $e->getMessage());
                $this->dispatch('validation-failed', message: 'File validation error');
                Log::error('File validation exception: ' . $e->getMessage() . ' | User: ' . Auth::id());
                return;
            }
            
            // Check 6: File size check
            try {
                if ($this->file->getSize() <= 0) {
                    $this->addError('file', 'The uploaded file is empty. Please upload a valid file.');
                    $this->dispatch('validation-failed', message: 'File is empty');
                    Log::warning('BLOCKED: Attempt to save with empty file. User: ' . Auth::id());
                    return;
                }
            } catch (\Exception $e) {
                $this->addError('file', 'Error checking file size: ' . $e->getMessage());
                $this->dispatch('validation-failed', message: 'File size check error');
                Log::error('File size check exception: ' . $e->getMessage() . ' | User: ' . Auth::id());
                return;
            }
        }

        // Early validation: Document type must be selected BEFORE any other processing
        if (empty($this->document_type_id) || $this->document_type_id === null || $this->document_type_id === '' || $this->document_type_id === 0) {
            $this->addError('document_type_id', 'Please select a document type.');
            session()->flash('error', 'Please select a document type before saving.');
            return;
        }
        
        $documentType = DocumentType::find($this->document_type_id);
        
        if (!$documentType) {
            $this->addError('document_type_id', 'Invalid document type selected. Please select a valid document type.');
            session()->flash('error', 'Invalid document type. Please select a valid document type.');
            return;
        }

        // Validate file before proceeding if file is present
        if ($this->file) {
            $this->validateFile();
            if ($this->getErrorBag()->has('file')) {
                return; // Stop validation if file is invalid
            }
        }

        // Build validation rules
        $rules = [
            'document_type_id' => 'required|exists:document_types,id',
            'document_name' => 'required|string|max:255',
        ];
        
        // Early validation check for document_type_id
        if (!$this->document_type_id || $this->document_type_id === '') {
            $this->addError('document_type_id', 'Please select a document type.');
            return;
        }

        // File validation - STRICT for new documents
        if ($this->editingDocumentId) {
            // For edit mode, file is optional
            if ($this->file) {
                $rules['file'] = 'required|file|mimes:pdf,jpg,jpeg,png,heic|max:5120';
            }
        } else {
            // For new documents, file is MANDATORY
            $rules['file'] = 'required|file|mimes:pdf,jpg,jpeg,png,heic|max:5120';
        }

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

        // Validate all rules
        try {
            $validated = $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ensure file errors are properly displayed
            if ($e->errors() && isset($e->errors()['file'])) {
                foreach ($e->errors()['file'] as $error) {
                    $this->addError('file', $error);
                }
            }
            // Re-throw validation exception to show errors
            throw $e;
        }

        // FINAL CHECK: For new documents, ensure file is still present after validation
        if (!$this->editingDocumentId) {
            if (!$this->file || !$this->file->isValid()) {
                $this->addError('file', 'Document file is required. Please upload a file before saving.');
                return;
            }
        }

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
                // Create new document - ABSOLUTE FINAL file check before service call
                // Triple-check: null, isset, empty, isValid, size
                if (is_null($this->file) || !isset($this->file) || empty($this->file)) {
                    $this->addError('file', 'Document file is required. Please upload a file before saving.');
                    Log::error('CRITICAL: Attempted to create document without file despite validation. User: ' . Auth::id() . ' | File state: ' . var_export($this->file, true));
                    session()->flash('error', 'Cannot save document without a file. Please upload a file first.');
                    return;
                }
                
                try {
                    if (!$this->file->isValid()) {
                        $this->addError('file', 'Document file is invalid. Please upload a valid file.');
                        Log::error('CRITICAL: Attempted to create document with invalid file. User: ' . Auth::id());
                        return;
                    }
                    
                    if ($this->file->getSize() <= 0) {
                        $this->addError('file', 'Document file is empty. Please upload a valid file.');
                        Log::error('CRITICAL: Attempted to create document with empty file. User: ' . Auth::id());
                        return;
                    }
                } catch (\Exception $e) {
                    $this->addError('file', 'Error validating file: ' . $e->getMessage());
                    Log::error('CRITICAL: File validation exception. User: ' . Auth::id() . ' | Error: ' . $e->getMessage());
                    return;
                }

                // Final check: Ensure file is still valid before service call
                if (!($this->file instanceof \Illuminate\Http\UploadedFile || $this->file instanceof TemporaryUploadedFile)) {
                    $this->addError('file', 'Invalid file object. Please upload a file again.');
                    Log::error('CRITICAL: File is not a valid UploadedFile instance. User: ' . Auth::id());
                    return;
                }

                try {
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
                } catch (\InvalidArgumentException $e) {
                    // Catch service-level validation errors (file is null/invalid)
                    $this->addError('file', $e->getMessage());
                    Log::error('Service validation failed: ' . $e->getMessage() . ' | User: ' . Auth::id());
                    session()->flash('error', $e->getMessage());
                    return;
                } catch (\Exception $e) {
                    Log::error('Document upload service error: ' . $e->getMessage() . ' | User: ' . Auth::id());
                    $this->addError('file', 'Failed to save document: ' . $e->getMessage());
                    return;
                }
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

    public function getCanSaveProperty()
    {
        // For edit mode, always allow save
        if ($this->editingDocumentId) {
            return true;
        }
        
        // For new documents, file must exist and be valid
        if ($this->file === null || !isset($this->file)) {
            return false;
        }
        
        try {
            return $this->file->isValid();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function render()
    {
        return view('livewire.documents.document-upload', [
            'documentTypes' => $this->documentTypes,
            'selectedDocumentType' => $this->selectedDocumentType,
        ]);
    }
}
