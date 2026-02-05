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
    public $activeTab = 'all'; // all, expired
    
    // Separate filters for expired documents
    public $expiredSearch = '';
    public $expiredSelectedType = 'all';
    public $expiredSelectedStatus = 'all';
    public $expiredSortBy = 'expiry_date'; // newest, oldest, expiry_date

    protected $queryString = [
        'selectedType' => ['except' => 'all'],
        'selectedStatus' => ['except' => 'all'],
        'expiryFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'newest'],
        'activeTab' => ['except' => 'all'],
        'expiredSearch' => ['except' => ''],
        'expiredSelectedType' => ['except' => 'all'],
        'expiredSelectedStatus' => ['except' => 'all'],
        'expiredSortBy' => ['except' => 'expiry_date'],
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

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function updatingExpiredSearch()
    {
        $this->resetPage();
    }

    public function updatingExpiredSelectedType()
    {
        $this->resetPage();
    }

    public function updatingExpiredSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingExpiredSortBy()
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

        // Filter by active tab
        if ($this->activeTab === 'expired') {
            $query->expired();
        }

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

        // Filter by expiry (only if not on expired tab)
        if ($this->activeTab !== 'expired') {
            if ($this->expiryFilter === 'expiring') {
                $query->expiringSoon();
            } elseif ($this->expiryFilter === 'expired') {
                $query->expired();
            }
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

    public function getExpiredDocumentsProperty()
    {
        $query = Auth::user()->documents()
            ->expired()
            ->with(['documentType', 'verificationLevel']);

        // Filter by type
        if ($this->expiredSelectedType !== 'all') {
            $query->whereHas('documentType', function($q) {
                $q->where('slug', $this->expiredSelectedType);
            });
        }

        // Filter by status
        if ($this->expiredSelectedStatus !== 'all') {
            $query->where('status', $this->expiredSelectedStatus);
        }

        // Search
        if ($this->expiredSearch) {
            $query->where(function($q) {
                $q->where('document_name', 'like', '%' . $this->expiredSearch . '%')
                  ->orWhere('document_number', 'like', '%' . $this->expiredSearch . '%')
                  ->orWhere('notes', 'like', '%' . $this->expiredSearch . '%');
            });
        }

        // Sort
        switch ($this->expiredSortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'expiry_date':
            default:
                $query->orderBy('expiry_date', 'asc');
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
            'expiredDocuments' => $this->expiredDocuments,
            'stats' => $this->stats,
            'expiringDocuments' => $this->expiringDocuments,
            'documentTypes' => $this->documentTypes,
        ])->layout('layouts.app-laravel');
    }
}
