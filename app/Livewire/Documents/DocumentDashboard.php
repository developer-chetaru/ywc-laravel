<?php

namespace App\Livewire\Documents;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessDocumentOcr;
use Carbon\Carbon;

class DocumentDashboard extends Component
{
    use WithPagination;

    public $selectedType = 'all';
    public $selectedStatus = 'all';
    public $expiryFilter = 'all'; // all, expiring, expired
    public $search = '';
    public $sortBy = 'newest'; // newest, oldest, expiry_date

    protected $queryString = [
        'selectedType' => ['except' => 'all'],
        'selectedStatus' => ['except' => 'all'],
        'expiryFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'newest'],
    ];

    public function mount()
    {
        // Initialize component
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingExpiryFilter()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $user = Auth::user();
        $documents = $user->documents();

        return [
            'total' => $documents->count(),
            'pending' => $documents->where('status', 'pending')->count(),
            'approved' => $documents->where('status', 'approved')->count(),
            'rejected' => $documents->where('status', 'rejected')->count(),
            'expiring_soon' => $documents->expiringSoon()->count(),
            'expired' => $documents->expired()->count(),
        ];
    }

    public function getExpiringDocumentsProperty()
    {
        return Auth::user()
            ->documents()
            ->expiringSoonOrExpired()
            ->with(['documentType', 'verificationLevel'])
            ->select('documents.*') // Ensure all fields including status are loaded
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();
    }

    public function getDocumentsProperty()
    {
        $query = Auth::user()->documents()->with(['documentType', 'verificationLevel']);

        // Filter by type
        if ($this->selectedType !== 'all') {
            $query->whereHas('documentType', function($q) {
                $q->where('slug', $this->selectedType);
            });
        }

        // Filter by status
        if ($this->selectedStatus !== 'all') {
            $query->where('status', $this->selectedStatus);
        }

        // Filter by expiry
        if ($this->expiryFilter === 'expiring') {
            $query->expiringSoon();
        } elseif ($this->expiryFilter === 'expired') {
            $query->expired();
        }

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('document_name', 'like', '%' . $this->search . '%')
                  ->orWhere('document_number', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        // Sort
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'expiry_date':
                $query->orderBy('expiry_date', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(20);
    }

    public function getDocumentTypesProperty()
    {
        return DocumentType::active()->ordered()->get();
    }

    public function retryOcr($documentId)
    {
        $document = Auth::user()->documents()->findOrFail($documentId);
        
        // Reset OCR status
        $document->update([
            'ocr_status' => 'pending',
            'ocr_error' => null,
        ]);
        
        // Dispatch OCR job
        ProcessDocumentOcr::dispatch($document);
        
        session()->flash('message', 'OCR processing has been restarted. Please wait a few moments.');
    }

    public function render()
    {
        return view('livewire.documents.document-dashboard', [
            'documents' => $this->documents,
            'stats' => $this->stats,
            'expiringDocuments' => $this->expiringDocuments,
            'documentTypes' => $this->documentTypes,
        ])->layout('layouts.app-laravel');
    }
}
