<?php

namespace App\Livewire\CareerHistory;

use Livewire\Component;
use App\Models\CareerHistoryEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CareerHistoryEntryDetailsModal extends Component
{
    public bool $showModal = false;
    public ?CareerHistoryEntry $entry = null;
    public ?int $previousEntryId = null;
    public ?int $nextEntryId = null;

    #[On('openCareerEntryDetails')]
    public function openModal(int $entryId, ?int $viewingUserId = null): void
    {
        $user = Auth::user();
        
        // Determine which user's entries to show
        $targetUserId = $viewingUserId;
        if (!$targetUserId) {
            // Try to get from parent component's viewingUserId
            // For now, find entry first and use its user_id
            $entry = CareerHistoryEntry::with('user')->findOrFail($entryId);
            $targetUserId = $entry->user_id;
        }

        // Check authorization: user can view own entries, super admin can view any
        if ($targetUserId !== $user->id && !$user->hasRole('super_admin')) {
            abort(403, 'Unauthorized to view this entry');
        }

        $targetUser = \App\Models\User::findOrFail($targetUserId);
        
        $this->entry = $targetUser->careerHistoryEntries()
            ->with([
                'referenceDocument.documentType',
                'contractDocument.documentType',
                'signoffDocument.documentType'
            ])
            ->findOrFail($entryId);

        // Find previous and next entries for navigation
        $this->findAdjacentEntries();

        $this->showModal = true;
    }

    protected function findAdjacentEntries(): void
    {
        if (!$this->entry) {
            return;
        }

        $user = $this->entry->user;
        $entries = $user->careerHistoryEntries()
            ->orderBy('start_date', 'desc')
            ->orderBy('id', 'desc')
            ->pluck('id')
            ->toArray();

        $currentIndex = array_search($this->entry->id, $entries);

        if ($currentIndex !== false) {
            $this->previousEntryId = $currentIndex > 0 ? $entries[$currentIndex - 1] : null;
            $this->nextEntryId = $currentIndex < count($entries) - 1 ? $entries[$currentIndex + 1] : null;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->entry = null;
        $this->previousEntryId = null;
        $this->nextEntryId = null;
    }

    public function navigateToEntry(?int $entryId): void
    {
        if ($entryId) {
            $this->openModal($entryId);
        }
    }

    public function edit(): void
    {
        if ($this->entry) {
            $this->dispatch('openCareerEntryModal', entryId: $this->entry->id);
            $this->closeModal();
        }
    }

    public function delete(): void
    {
        if ($this->entry) {
            $this->dispatch('deleteCareerEntry', entryId: $this->entry->id);
            $this->closeModal();
        }
    }

    public function duplicate(): void
    {
        if ($this->entry) {
            $this->dispatch('duplicateCareerEntry', entryId: $this->entry->id);
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.career-history.career-history-entry-details-modal');
    }
}
