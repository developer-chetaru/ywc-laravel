<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\User;
use App\Models\DocumentStatusChange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentStatusChangedMail;

class DocumentApproval extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedDocument = null;
    public $showModal = false;
    public $action = ''; // 'approve' or 'reject'
    public $notes = '';
    public $filterType = 'all'; // 'all', 'passport', 'idvisa', 'certificate', 'other'

    protected $paginationTheme = 'tailwind';

    public $token = null;
    public $showLogin = false;
    public $email = '';
    public $password = '';

    public function mount()
    {
        // Check if token is provided in URL
        $this->token = request()->query('token');
        
        // If no user is authenticated and no token provided, show login option
        if (!Auth::check() && !$this->token) {
            $this->showLogin = true;
            return;
        }

        // If token is provided, authenticate via token
        if ($this->token && !Auth::check()) {
            $this->authenticateWithToken();
        }

        // If still not authenticated, show login
        if (!Auth::check()) {
            $this->showLogin = true;
            return;
        }

        // Check if user has permission to approve documents (optional check)
        // Allow any authenticated user, or restrict to specific roles if needed
        // if (!Auth::user()->hasAnyRole(['super_admin', 'admin', 'verifier'])) {
        //     abort(403, 'You do not have permission to access this page.');
        // }
    }

    protected function authenticateWithToken()
    {
        // Try to find token in Sanctum personal access tokens
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($this->token);
        
        if ($tokenModel) {
            $user = $tokenModel->tokenable;
            if ($user) {
                Auth::login($user);
                $this->showLogin = false;
                return;
            }
        }

        // If Sanctum token doesn't work, try JWT token (if available)
        if (class_exists('\Tymon\JWTAuth\Facades\JWTAuth')) {
            try {
                $user = \Tymon\JWTAuth\Facades\JWTAuth::setToken($this->token)->authenticate();
                if ($user) {
                    Auth::login($user);
                    $this->showLogin = false;
                    return;
                }
            } catch (\Exception $e) {
                // Token invalid, will show login
            }
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->showLogin = false;
            $this->email = '';
            $this->password = '';
            session()->flash('success', 'Login successful!');
            $this->resetPage();
        } else {
            session()->flash('error', 'Invalid credentials. Please try again.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function openModal($documentId, $action)
    {
        $this->selectedDocument = Document::with(['user', 'documentType'])
            ->where('status', 'pending')
            ->findOrFail($documentId);
        
        $this->action = $action;
        $this->notes = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDocument = null;
        $this->action = '';
        $this->notes = '';
    }

    public function approveDocument()
    {
        if (!$this->selectedDocument) {
            return;
        }

        $oldStatus = $this->selectedDocument->status;

        $this->selectedDocument->update([
            'status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        // Create status change record
        DocumentStatusChange::create([
            'document_id' => $this->selectedDocument->id,
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'changed_by' => Auth::id(),
            'notes' => $this->notes ?: 'Document approved via approval page',
        ]);

        // Send notification email
        try {
            Mail::to($this->selectedDocument->user->email)
                ->send(new DocumentStatusChangedMail($this->selectedDocument, 'approved', $this->notes));
        } catch (\Exception $e) {
            \Log::error('Failed to send document approval email: ' . $e->getMessage());
        }

        session()->flash('success', 'Document approved successfully!');
        $this->closeModal();
        $this->resetPage();
    }

    public function rejectDocument()
    {
        if (!$this->selectedDocument) {
            return;
        }

        $oldStatus = $this->selectedDocument->status;

        $this->selectedDocument->update([
            'status' => 'rejected',
            'updated_by' => Auth::id(),
        ]);

        // Create status change record
        DocumentStatusChange::create([
            'document_id' => $this->selectedDocument->id,
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'changed_by' => Auth::id(),
            'notes' => $this->notes ?: 'Document rejected via approval page',
        ]);

        // Send notification email
        try {
            Mail::to($this->selectedDocument->user->email)
                ->send(new DocumentStatusChangedMail($this->selectedDocument, 'rejected', $this->notes));
        } catch (\Exception $e) {
            \Log::error('Failed to send document rejection email: ' . $e->getMessage());
        }

        session()->flash('success', 'Document rejected successfully!');
        $this->closeModal();
        $this->resetPage();
    }

    public function getDocumentUrl($document)
    {
        if (!$document->file_path) {
            return null;
        }

        // Check if it's a full URL or a storage path
        if (filter_var($document->file_path, FILTER_VALIDATE_URL)) {
            return $document->file_path;
        }

        // Check if file exists in storage
        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->url($document->file_path);
        }

        // Try other storage disks
        foreach (['local', 's3'] as $disk) {
            if (Storage::disk($disk)->exists($document->file_path)) {
                return Storage::disk($disk)->url($document->file_path);
            }
        }

        return null;
    }

    public function render()
    {
        $query = Document::with(['user', 'documentType'])
            ->where('status', 'pending')
            ->whereNull('deleted_at');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('document_name', 'like', '%' . $this->search . '%')
                    ->orWhere('document_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply type filter
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.documents.document-approval', [
            'documents' => $documents,
        ])->layout('layouts.app-laravel');
    }
}
