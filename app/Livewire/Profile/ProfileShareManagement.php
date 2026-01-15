<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProfileShare;
use App\Services\Documents\ProfileShareService;
use Illuminate\Support\Facades\Auth;

class ProfileShareManagement extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $sharedSections = ['personal_info', 'documents', 'career_history'];
    public $documentCategories = [];
    public $careerEntryIds = [];
    public $recipientEmail = '';
    public $recipientName = '';
    public $personalMessage = '';
    public $expiresInDays = null;
    public $generateQrCode = false;

    protected $shareService;

    public function boot(ProfileShareService $shareService)
    {
        $this->shareService = $shareService;
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->reset(['sharedSections', 'documentCategories', 'careerEntryIds', 'recipientEmail', 'recipientName', 'personalMessage', 'expiresInDays', 'generateQrCode']);
        $this->sharedSections = ['personal_info', 'documents', 'career_history'];
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function createShare()
    {
        $this->validate([
            'sharedSections' => 'required|array|min:1',
            'sharedSections.*' => 'in:personal_info,documents,career_history',
            'documentCategories' => 'nullable|array',
            'careerEntryIds' => 'nullable|array',
            'careerEntryIds.*' => 'exists:career_history_entries,id',
            'recipientEmail' => 'nullable|email',
            'recipientName' => 'nullable|string|max:255',
            'personalMessage' => 'nullable|string|max:500',
            'expiresInDays' => 'nullable|integer|min:1|max:365',
        ]);

        try {
            $share = $this->shareService->createShare(
                Auth::user(),
                $this->sharedSections,
                $this->documentCategories ?: null,
                $this->careerEntryIds ?: null,
                $this->recipientEmail ?: null,
                $this->recipientName ?: null,
                $this->personalMessage ?: null,
                $this->expiresInDays ? \Carbon\Carbon::now()->addDays($this->expiresInDays) : null,
                $this->generateQrCode
            );

            session()->flash('message', 'Profile share created successfully!');
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->addError('create', $e->getMessage());
        }
    }

    public function revokeShare($shareId)
    {
        $share = ProfileShare::where('user_id', Auth::id())->findOrFail($shareId);
        $this->shareService->revokeShare($share);
        session()->flash('message', 'Profile share revoked successfully');
    }

    public function generateQrCode($shareId)
    {
        $share = ProfileShare::where('user_id', Auth::id())->findOrFail($shareId);
        $qrCodePath = $this->shareService->generateQrCode($share);
        $share->update(['qr_code_path' => $qrCodePath]);
        session()->flash('message', 'QR code generated successfully');
    }

    public function getSharesProperty()
    {
        return $this->shareService->getUserShares(Auth::user());
    }

    public function getDocumentTypesProperty()
    {
        return \App\Models\DocumentType::active()->ordered()->get();
    }

    public function getCareerEntriesProperty()
    {
        return Auth::user()->careerHistoryEntries()->orderBy('start_date', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.profile.profile-share-management', [
            'shares' => $this->shares,
            'documentTypes' => $this->documentTypes,
            'careerEntries' => $this->careerEntries,
        ])->layout('layouts.app-laravel');
    }
}
