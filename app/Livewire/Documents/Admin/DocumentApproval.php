<?php

namespace App\Livewire\Documents\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\User;
use App\Models\DocumentStatusChange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentStatusChangedMail;
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
    public $selectedDocuments = []; // For batch actions
    public $selectAll = false;

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

        $oldStatus = $this->selectedDocument->status;
        
        $this->selectedDocument->update([
            'status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        // Track status change
        DocumentStatusChange::create([
            'document_id' => $this->selectedDocument->id,
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'notes' => $this->approvalNotes,
            'changed_by' => Auth::id(),
        ]);

        // Send notification email
        try {
            Mail::to($this->selectedDocument->user->email)
                ->send(new DocumentStatusChangedMail($this->selectedDocument, 'approved', $this->approvalNotes));
        } catch (\Exception $e) {
            \Log::error('Failed to send document approval email: ' . $e->getMessage());
        }

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

        $oldStatus = $this->selectedDocument->status;
        
        $this->selectedDocument->update([
            'status' => 'rejected',
            'updated_by' => Auth::id(),
        ]);

        // Track status change
        DocumentStatusChange::create([
            'document_id' => $this->selectedDocument->id,
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'notes' => $this->rejectionNotes,
            'changed_by' => Auth::id(),
        ]);

        // Send notification email
        try {
            Mail::to($this->selectedDocument->user->email)
                ->send(new DocumentStatusChangedMail($this->selectedDocument, 'rejected', $this->rejectionNotes));
        } catch (\Exception $e) {
            \Log::error('Failed to send document rejection email: ' . $e->getMessage());
        }

        session()->flash('message', 'Document rejected');
        $this->closeModal();
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedDocuments = $this->documents->pluck('id')->toArray();
        } else {
            $this->selectedDocuments = [];
        }
    }

    public function batchApprove()
    {
        if (empty($this->selectedDocuments)) {
            session()->flash('error', 'Please select at least one document');
            return;
        }

        $documents = Document::whereIn('id', $this->selectedDocuments)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        $count = 0;
        foreach ($documents as $document) {
            $oldStatus = $document->status;
            
            $document->update([
                'status' => 'approved',
                'updated_by' => Auth::id(),
            ]);

            // Track status change
            DocumentStatusChange::create([
                'document_id' => $document->id,
                'old_status' => $oldStatus,
                'new_status' => 'approved',
                'notes' => 'Batch approved',
                'changed_by' => Auth::id(),
            ]);

            // Send notification email
            try {
                Mail::to($document->user->email)
                    ->send(new DocumentStatusChangedMail($document, 'approved', 'Batch approved'));
            } catch (\Exception $e) {
                \Log::error('Failed to send batch approval email: ' . $e->getMessage());
            }

            $count++;
        }

        $this->selectedDocuments = [];
        $this->selectAll = false;
        session()->flash('message', $count . ' document(s) approved');
        $this->resetPage();
    }

    public function batchReject()
    {
        if (empty($this->selectedDocuments)) {
            session()->flash('error', 'Please select at least one document');
            return;
        }

        $this->action = 'batch-reject';
        $this->showModal = true;
    }

    public function confirmBatchReject()
    {
        $this->validate([
            'rejectionNotes' => 'required|string|max:500',
        ]);

        $documents = Document::whereIn('id', $this->selectedDocuments)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        $count = 0;
        foreach ($documents as $document) {
            $oldStatus = $document->status;
            
            $document->update([
                'status' => 'rejected',
                'updated_by' => Auth::id(),
            ]);

            // Track status change
            DocumentStatusChange::create([
                'document_id' => $document->id,
                'old_status' => $oldStatus,
                'new_status' => 'rejected',
                'notes' => $this->rejectionNotes,
                'changed_by' => Auth::id(),
            ]);

            // Send notification email
            try {
                Mail::to($document->user->email)
                    ->send(new DocumentStatusChangedMail($document, 'rejected', $this->rejectionNotes));
            } catch (\Exception $e) {
                \Log::error('Failed to send batch rejection email: ' . $e->getMessage());
            }

            $count++;
        }

        $this->selectedDocuments = [];
        $this->selectAll = false;
        $this->rejectionNotes = '';
        $this->action = '';
        $this->showModal = false;
        session()->flash('message', $count . ' document(s) rejected');
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
