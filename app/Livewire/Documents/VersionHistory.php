<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\Documents\VersionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class VersionHistory extends Component
{
    public ?Document $document = null;
    public $versions = [];
    public bool $showRestoreConfirm = false;
    public bool $showPreview = false;
    public bool $showComparison = false;
    public ?int $versionToRestore = null;
    public ?int $previewVersionId = null;
    public ?int $compareVersionId1 = null;
    public ?int $compareVersionId2 = null;
    public $selectedVersionsForCleanup = [];

    public ?int $documentId = null;

    public function mount(?int $documentId = null): void
    {
        if ($documentId) {
            $this->documentId = $documentId;
            $this->loadDocument();
        }
    }

    #[On('openVersionHistory')]
    public function openHistory($documentId): void
    {
        $this->documentId = is_array($documentId) ? ($documentId['documentId'] ?? $documentId[0] ?? null) : $documentId;
        if ($this->documentId) {
            $this->loadDocument();
        }
    }

    protected function loadDocument(): void
    {
        if ($this->documentId) {
            $this->document = Auth::user()->documents()->findOrFail($this->documentId);
            $this->loadVersions();
        }
    }

    public function loadVersions(): void
    {
        if (!$this->document) {
            return;
        }

        $this->versions = DocumentVersion::where('document_id', $this->document->id)
            ->with('creator')
            ->latest('version_number')
            ->get();
    }

    public function confirmRestore(int $versionId): void
    {
        $this->versionToRestore = $versionId;
        $this->showRestoreConfirm = true;
    }

    public function cancelRestore(): void
    {
        $this->showRestoreConfirm = false;
        $this->versionToRestore = null;
    }

    public function restoreVersion(): void
    {
        if (!$this->document || !$this->versionToRestore) {
            return;
        }

        $version = DocumentVersion::where('document_id', $this->document->id)
            ->where('id', $this->versionToRestore)
            ->firstOrFail();

        try {
            $versionService = app(VersionService::class);
            $versionService->restoreVersion($this->document, $version);
            
            $this->dispatch('versionRestored', documentId: $this->document->id);
            $this->loadVersions();
            $this->showRestoreConfirm = false;
            $this->versionToRestore = null;
            
            session()->flash('version_message', 'Document restored to version ' . $version->version_number . ' successfully!');
        } catch (\Exception $e) {
            session()->flash('version_error', 'Failed to restore version: ' . $e->getMessage());
        }
    }

    public function previewVersion(int $versionId): void
    {
        $this->previewVersionId = $versionId;
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->previewVersionId = null;
    }

    public function getPreviewVersionProperty()
    {
        if (!$this->previewVersionId) {
            return null;
        }
        return DocumentVersion::where('document_id', $this->document->id)
            ->where('id', $this->previewVersionId)
            ->first();
    }

    public function openComparison(): void
    {
        $this->showComparison = true;
    }

    public function closeComparison(): void
    {
        $this->showComparison = false;
        $this->compareVersionId1 = null;
        $this->compareVersionId2 = null;
    }

    public function getComparisonVersionsProperty()
    {
        $versions = [];
        if ($this->compareVersionId1) {
            $versions['v1'] = DocumentVersion::where('document_id', $this->document->id)
                ->where('id', $this->compareVersionId1)
                ->first();
        }
        if ($this->compareVersionId2) {
            $versions['v2'] = DocumentVersion::where('document_id', $this->document->id)
                ->where('id', $this->compareVersionId2)
                ->first();
        }
        return $versions;
    }

    public function toggleVersionForCleanup(int $versionId): void
    {
        if (in_array($versionId, $this->selectedVersionsForCleanup)) {
            $this->selectedVersionsForCleanup = array_diff($this->selectedVersionsForCleanup, [$versionId]);
        } else {
            $this->selectedVersionsForCleanup[] = $versionId;
        }
    }

    public function bulkCleanup(): void
    {
        if (empty($this->selectedVersionsForCleanup)) {
            session()->flash('version_error', 'Please select versions to delete.');
            return;
        }

        try {
            $deletedCount = 0;
            foreach ($this->selectedVersionsForCleanup as $versionId) {
                $version = DocumentVersion::where('document_id', $this->document->id)
                    ->where('id', $versionId)
                    ->first();
                
                if ($version && $version->version_number != $this->document->version) {
                    // Delete files
                    if ($version->file_path && \Storage::disk('public')->exists($version->file_path)) {
                        \Storage::disk('public')->delete($version->file_path);
                    }
                    if ($version->thumbnail_path && \Storage::disk('public')->exists($version->thumbnail_path)) {
                        \Storage::disk('public')->delete($version->thumbnail_path);
                    }
                    $version->delete();
                    $deletedCount++;
                }
            }
            
            $this->selectedVersionsForCleanup = [];
            $this->loadVersions();
            session()->flash('version_message', "Successfully deleted {$deletedCount} version(s).");
        } catch (\Exception $e) {
            session()->flash('version_error', 'Failed to delete versions: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.documents.version-history');
    }
}
