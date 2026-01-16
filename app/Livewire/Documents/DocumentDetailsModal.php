<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class DocumentDetailsModal extends Component
{
    public bool $showModal = false;
    public ?Document $document = null;
    public ?string $signedUrl = null;

    #[On('openDocumentDetails')]
    public function openModal(int $documentId): void
    {
        $this->document = Auth::user()->documents()
            ->with([
                'documentType',
                'passportDetail',
                'idvisaDetail',
                'certificates.certificateType',
                'certificates.certificateIssuer',
                'otherDocument',
                'uploader',
                'updater',
                'statusChanges.changedBy'
            ])
            ->findOrFail($documentId);

        // Generate signed URL for secure access
        if ($this->document->file_path && Storage::disk('public')->exists($this->document->file_path)) {
            $this->signedUrl = route('documents.signed-url', ['document' => $this->document->id]);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->document = null;
        $this->signedUrl = null;
    }

    public function download(): void
    {
        if ($this->document && $this->document->file_path) {
            $this->dispatch('downloadDocument', documentId: $this->document->id);
        }
    }

    public function edit(): void
    {
        if ($this->document) {
            $this->dispatch('openUploadModal', documentId: $this->document->id, mode: 'edit');
            $this->closeModal();
        }
    }

    public function resubmit(): void
    {
        if ($this->document && $this->document->status === 'rejected') {
            $this->dispatch('openUploadModal', documentId: $this->document->id, mode: 'edit');
            $this->closeModal();
        }
    }

    public function delete(): void
    {
        if ($this->document) {
            $this->dispatch('deleteDocument', documentId: $this->document->id);
            $this->closeModal();
        }
    }

    public function share(): void
    {
        if ($this->document) {
            $this->dispatch('openShareDocumentsModal', selectedDocuments: [$this->document->id]);
            $this->closeModal();
        }
    }

    public function getFileExtension(): ?string
    {
        if (!$this->document || !$this->document->file_path) {
            return null;
        }
        return strtolower(pathinfo($this->document->file_path, PATHINFO_EXTENSION));
    }

    public function isImage(): bool
    {
        $ext = $this->getFileExtension();
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    }

    public function isPdf(): bool
    {
        return $this->getFileExtension() === 'pdf';
    }

    public function render()
    {
        return view('livewire.documents.document-details-modal');
    }
}
