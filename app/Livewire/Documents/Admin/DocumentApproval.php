<?php

namespace App\Livewire\Documents\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DocumentApproval extends Component
{
    use WithPagination;

    public $selectedStatus = 'pending';
    public $search = '';
    public $selectedUserId = null;
    public $showModal = false;
    public $selectedDocument = null;
    public $approvalNotes = '';
    public $rejectionNotes = '';
    public $action = ''; // 'approve' or 'reject'

    protected $queryString = [
        'selectedStatus' => ['except' => 'pending'],
        'search' => ['except' => ''],
        'selectedUserId' => ['except' => ''],
    ];

    public function mount()
    {
        // Check admin permissions
        if (!Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
    }

    public function openModal($documentId, $action)
    {
        $this->selectedDocument = Document::with(['user', 'documentType'])->findOrFail($documentId);
        $this->action = $action;
        $this->approvalNotes = '';
        $this->rejectionNotes = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDocument = null;
        $this->action = '';
        $this->approvalNotes = '';
        $this->rejectionNotes = '';
    }

    public function approve()
    {
        $this->validate([
            'approvalNotes' => 'nullable|string|max:500',
        ]);

        if (!$this->selectedDocument) {
            return;
        }

        $this->selectedDocument->update([
            'status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        // TODO: Send notification email to user
        // Mail::to($this->selectedDocument->user->email)->send(new DocumentApprovedMail($this->selectedDocument, $this->approvalNotes));

        session()->flash('message', 'Document approved successfully');
        $this->closeModal();
        $this->resetPage();
    }

    public function reject()
    {
        $this->validate([
            'rejectionNotes' => 'required|string|max:500',
        ]);

        if (!$this->selectedDocument) {
            return;
        }

        $this->selectedDocument->update([
            'status' => 'rejected',
            'updated_by' => Auth::id(),
        ]);

        // TODO: Send notification email to user with rejection notes
        // Mail::to($this->selectedDocument->user->email)->send(new DocumentRejectedMail($this->selectedDocument, $this->rejectionNotes));

        session()->flash('message', 'Document rejected');
        $this->closeModal();
        $this->resetPage();
    }

    public function batchApprove($documentIds)
    {
        Document::whereIn('id', $documentIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'updated_by' => Auth::id(),
            ]);

        session()->flash('message', count($documentIds) . ' documents approved');
        $this->resetPage();
    }

    public function getDocumentsProperty()
    {
        $query = Document::with(['user', 'documentType']);

        // Filter by status
        if ($this->selectedStatus !== 'all') {
            $query->where('status', $this->selectedStatus);
        }

        // Filter by user
        if ($this->selectedUserId) {
            $query->where('user_id', $this->selectedUserId);
        }

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('document_name', 'like', '%' . $this->search . '%')
                  ->orWhere('document_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getUsersProperty()
    {
        return User::whereHas('documents', function($q) {
            if ($this->selectedStatus !== 'all') {
                $q->where('status', $this->selectedStatus);
            }
        })->orderBy('first_name')->get();
    }

    public function render()
    {
        return view('livewire.documents.admin.document-approval', [
            'documents' => $this->documents,
            'users' => $this->users,
        ])->layout('layouts.app-laravel');
    }
}
