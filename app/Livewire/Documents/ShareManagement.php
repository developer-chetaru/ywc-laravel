<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DocumentShare;
use App\Services\Documents\DocumentShareService;
use Illuminate\Support\Facades\Auth;

class ShareManagement extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $selectedDocuments = [];
    public $recipientEmail = '';
    public $recipientName = '';
    public $personalMessage = '';
    public $expiresInDays = null;

    protected $shareService;

    public function boot(DocumentShareService $shareService)
    {
        $this->shareService = $shareService;
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->reset(['selectedDocuments', 'recipientEmail', 'recipientName', 'personalMessage', 'expiresInDays']);
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function createShare()
    {
        $this->validate([
            'selectedDocuments' => 'required|array|min:1',
            'selectedDocuments.*' => 'exists:documents,id',
            'recipientEmail' => 'nullable|email',
            'recipientName' => 'nullable|string|max:255',
            'personalMessage' => 'nullable|string|max:500',
            'expiresInDays' => 'nullable|integer|min:1|max:365',
        ]);

        try {
            $share = $this->shareService->createShare(
                Auth::user(),
                $this->selectedDocuments,
                $this->recipientEmail ?: null,
                $this->recipientName ?: null,
                $this->personalMessage ?: null,
                $this->expiresInDays ? \Carbon\Carbon::now()->addDays($this->expiresInDays) : null
            );

            session()->flash('message', 'Share created successfully!');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->addError('create', $e->getMessage());
        }
    }

    public function revokeShare($shareId)
    {
        $share = DocumentShare::where('user_id', Auth::id())->findOrFail($shareId);
        $this->shareService->revokeShare($share);
        session()->flash('message', 'Share revoked successfully');
    }

    public function resendEmail($shareId)
    {
        $share = DocumentShare::where('user_id', Auth::id())->findOrFail($shareId);
        
        if (!$share->recipient_email) {
            $this->addError('resend', 'No recipient email associated with this share');
            return;
        }

        \Mail::to($share->recipient_email)->send(new \App\Mail\DocumentShareMail($share, Auth::user()));
        session()->flash('message', 'Email resent successfully');
    }

    public function getSharesProperty()
    {
        return $this->shareService->getUserShares(Auth::user());
    }

    public function getAvailableDocumentsProperty()
    {
        return Auth::user()
            ->documents()
            ->with('documentType')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.documents.share-management', [
            'shares' => $this->shares,
            'availableDocuments' => $this->availableDocuments,
        ])->layout('layouts.app-laravel');
    }
}
