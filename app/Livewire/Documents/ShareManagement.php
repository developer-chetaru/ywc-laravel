<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DocumentShare;
use App\Models\ShareTemplate;
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
    public $selectedTemplateId = null;
    public $showTemplatePreview = false;

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
            // If template is selected, use template's settings
            if ($this->selectedTemplateId) {
                $template = ShareTemplate::where('user_id', Auth::id())->findOrFail($this->selectedTemplateId);
                
                // Use template's expiry if not manually set
                $expiresInDays = $this->expiresInDays ?? $template->expiry_duration_days;
                // Use template's message if personal message not provided
                $message = $this->personalMessage ?: $template->default_message;
                
                $share = $this->shareService->createShare(
                    Auth::user(),
                    $this->selectedDocuments,
                    $this->recipientEmail ?: null,
                    $this->recipientName ?: null,
                    $message,
                    $expiresInDays ? \Carbon\Carbon::now()->addDays($expiresInDays) : null,
                    $template->permissions ?? []
                );

                session()->flash('message', 'Share created successfully using template!');
                $this->closeCreateModal();
                $this->resetPage();
                return;
            }

            // Fallback to regular share creation
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

    public function getTemplatesProperty()
    {
        return ShareTemplate::forUser(Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.documents.share-management', [
            'shares' => $this->shares,
            'availableDocuments' => $this->availableDocuments,
            'templates' => $this->templates,
        ])->layout('layouts.app-laravel');
    }
}
