<?php

namespace App\Livewire\CareerHistory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\CareerHistoryEntry;
use App\Models\Document;
use Carbon\Carbon;

class CareerHistoryManager extends Component
{
    use WithPagination;

    // For super admin viewing other users
    public $viewingUserId = null;
    public $isSuperAdmin = false;
    public $viewingUser = null;
    public $search = '';
    public $showUserSelector = false;

    // Form fields
    public $showModal = false;
    public $editingId = null;
    
    // Vessel Information
    public $vessel_name = '';
    public $position_title = '';
    public $vessel_type = '';
    public $vessel_flag = '';
    public $vessel_length_meters = '';
    public $gross_tonnage = '';
    
    // Position Details
    public $start_date = '';
    public $end_date = '';
    public $is_current_position = false;
    public $employment_type = '';
    public $position_rank = '';
    public $department = '';
    
    // Employment Information
    public $employer_company = '';
    public $supervisor_name = '';
    public $supervisor_contact = '';
    public $key_duties = '';
    public $notable_achievements = '';
    public $departure_reason = '';
    
    // Documentation Links
    public $reference_document_id = null;
    public $contract_document_id = null;
    public $signoff_document_id = null;
    
    // Visibility
    public $visible_on_profile = true;
    public $display_order = 0;

    // Vessel types, ranks, departments, etc.
    public $vesselTypes = [
        'motor_yacht' => 'Motor Yacht',
        'sailing_yacht' => 'Sailing Yacht',
        'explorer_yacht' => 'Explorer Yacht',
        'catamaran' => 'Catamaran',
        'commercial_vessel' => 'Commercial Vessel',
        'other' => 'Other',
    ];

    public $employmentTypes = [
        'permanent' => 'Permanent',
        'seasonal' => 'Seasonal',
        'temporary' => 'Temporary',
        'rotational_contract' => 'Rotational Contract',
    ];

    public $positionRanks = [
        'captain' => 'Captain',
        'officer' => 'Officer',
        'junior_crew' => 'Junior Crew',
        'support_staff' => 'Support Staff',
    ];

    public $departments = [
        'deck' => 'Deck',
        'engineering' => 'Engineering',
        'interior' => 'Interior',
        'galley' => 'Galley',
        'other' => 'Other',
    ];

    public $departureReasons = [
        'contract_end' => 'Contract End',
        'new_opportunity' => 'New Opportunity',
        'personal_reasons' => 'Personal Reasons',
        'vessel_sold' => 'Vessel Sold',
        'other' => 'Other',
    ];

    public function mount($userId = null)
    {
        $currentUser = Auth::user();
        $this->isSuperAdmin = $currentUser->hasRole('super_admin');
        
        // If super admin and userId provided, view that user's entries
        if ($this->isSuperAdmin && $userId) {
            $this->viewingUserId = $userId;
            $this->viewingUser = \App\Models\User::findOrFail($userId);
        } else {
            $this->viewingUserId = $currentUser->id;
            $this->viewingUser = $currentUser;
        }
    }

    public function selectUser($userId)
    {
        $this->viewingUserId = $userId;
        $this->viewingUser = \App\Models\User::findOrFail($userId);
        $this->showUserSelector = false;
        $this->resetPage();
    }

    public function viewMyCareer()
    {
        $this->viewingUserId = Auth::id();
        $this->viewingUser = Auth::user();
        $this->showUserSelector = false;
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        if (!$this->isSuperAdmin) {
            return collect();
        }

        $query = \App\Models\User::query()
            ->whereHas("roles", function ($q) {
                $q->where("name", "!=", "super_admin");
            });
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('first_name')->orderBy('last_name')->limit(20)->get();
    }

    public function openModal($entryId = null)
    {
        // Only allow editing own entries (or super admin can edit any)
        if (!$this->isSuperAdmin && $this->viewingUserId !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $this->editingId = $entryId;
        $this->resetForm();
        
        if ($entryId) {
            $entry = CareerHistoryEntry::where('user_id', $this->viewingUserId)->findOrFail($entryId);
            $this->vessel_name = $entry->vessel_name;
            $this->position_title = $entry->position_title;
            $this->vessel_type = $entry->vessel_type ?? '';
            $this->vessel_flag = $entry->vessel_flag ?? '';
            $this->vessel_length_meters = $entry->vessel_length_meters ?? '';
            $this->gross_tonnage = $entry->gross_tonnage ?? '';
            $this->start_date = $entry->start_date ? $entry->start_date->format('Y-m-d') : '';
            $this->end_date = $entry->end_date ? $entry->end_date->format('Y-m-d') : '';
            $this->is_current_position = $entry->isCurrentPosition();
            $this->employment_type = $entry->employment_type ?? '';
            $this->position_rank = $entry->position_rank ?? '';
            $this->department = $entry->department ?? '';
            $this->employer_company = $entry->employer_company ?? '';
            $this->supervisor_name = $entry->supervisor_name ?? '';
            $this->supervisor_contact = $entry->supervisor_contact ?? '';
            $this->key_duties = $entry->key_duties ?? '';
            $this->notable_achievements = $entry->notable_achievements ?? '';
            $this->departure_reason = $entry->departure_reason ?? '';
            $this->reference_document_id = $entry->reference_document_id;
            $this->contract_document_id = $entry->contract_document_id;
            $this->signoff_document_id = $entry->signoff_document_id;
            $this->visible_on_profile = $entry->visible_on_profile;
            $this->display_order = $entry->display_order;
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->vessel_name = '';
        $this->position_title = '';
        $this->vessel_type = '';
        $this->vessel_flag = '';
        $this->vessel_length_meters = '';
        $this->gross_tonnage = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_current_position = false;
        $this->employment_type = '';
        $this->position_rank = '';
        $this->department = '';
        $this->employer_company = '';
        $this->supervisor_name = '';
        $this->supervisor_contact = '';
        $this->key_duties = '';
        $this->notable_achievements = '';
        $this->departure_reason = '';
        $this->reference_document_id = null;
        $this->contract_document_id = null;
        $this->signoff_document_id = null;
        $this->visible_on_profile = true;
        $this->display_order = 0;
    }

    public function updatedIsCurrentPosition()
    {
        if ($this->is_current_position) {
            $this->end_date = '';
        }
    }

    public function save()
    {
        $rules = [
            'vessel_name' => 'required|string|max:255',
            'position_title' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'vessel_type' => 'nullable|in:' . implode(',', array_keys($this->vesselTypes)),
            'vessel_flag' => 'nullable|string|max:3',
            'vessel_length_meters' => 'nullable|numeric|min:0|max:999999.99',
            'gross_tonnage' => 'nullable|integer|min:0',
            'employment_type' => 'nullable|in:' . implode(',', array_keys($this->employmentTypes)),
            'position_rank' => 'nullable|in:' . implode(',', array_keys($this->positionRanks)),
            'department' => 'nullable|in:' . implode(',', array_keys($this->departments)),
            'employer_company' => 'nullable|string|max:255',
            'supervisor_name' => 'nullable|string|max:255',
            'supervisor_contact' => 'nullable|string|max:255',
            'key_duties' => 'nullable|string|max:500',
            'notable_achievements' => 'nullable|string|max:500',
            'departure_reason' => 'nullable|in:' . implode(',', array_keys($this->departureReasons)),
            'reference_document_id' => 'nullable|exists:documents,id',
            'contract_document_id' => 'nullable|exists:documents,id',
            'signoff_document_id' => 'nullable|exists:documents,id',
            'visible_on_profile' => 'boolean',
            'display_order' => 'integer|min:0',
        ];

        // If current position, end_date should be null
        if ($this->is_current_position) {
            $this->end_date = null;
        }

        $validated = $this->validate($rules);

        // Verify documents belong to viewing user
        if ($validated['reference_document_id']) {
            $doc = Document::where('id', $validated['reference_document_id'])
                ->where('user_id', $this->viewingUserId)
                ->firstOrFail();
        }
        if ($validated['contract_document_id']) {
            $doc = Document::where('id', $validated['contract_document_id'])
                ->where('user_id', $this->viewingUserId)
                ->firstOrFail();
        }
        if ($validated['signoff_document_id']) {
            $doc = Document::where('id', $validated['signoff_document_id'])
                ->where('user_id', $this->viewingUserId)
                ->firstOrFail();
        }

        $data = [
            'user_id' => $this->viewingUserId,
            'vessel_name' => $validated['vessel_name'],
            'position_title' => $validated['position_title'],
            'vessel_type' => $validated['vessel_type'] ?: null,
            'vessel_flag' => $validated['vessel_flag'] ?: null,
            'vessel_length_meters' => $validated['vessel_length_meters'] ?: null,
            'gross_tonnage' => $validated['gross_tonnage'] ?: null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'employment_type' => $validated['employment_type'] ?: null,
            'position_rank' => $validated['position_rank'] ?: null,
            'department' => $validated['department'] ?: null,
            'employer_company' => $validated['employer_company'] ?: null,
            'supervisor_name' => $validated['supervisor_name'] ?: null,
            'supervisor_contact' => $validated['supervisor_contact'] ?: null,
            'key_duties' => $validated['key_duties'] ?: null,
            'notable_achievements' => $validated['notable_achievements'] ?: null,
            'departure_reason' => $validated['departure_reason'] ?: null,
            'reference_document_id' => $validated['reference_document_id'],
            'contract_document_id' => $validated['contract_document_id'],
            'signoff_document_id' => $validated['signoff_document_id'],
            'visible_on_profile' => $validated['visible_on_profile'],
            'display_order' => $validated['display_order'],
        ];

        // Only allow editing own entries (or super admin can edit any)
        if (!$this->isSuperAdmin && $this->viewingUserId !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($this->editingId) {
            $entry = CareerHistoryEntry::where('user_id', $this->viewingUserId)->findOrFail($this->editingId);
            $entry->update($data);
            session()->flash('message', 'Career entry updated successfully!');
        } else {
            CareerHistoryEntry::create($data);
            session()->flash('message', 'Career entry added successfully!');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete($entryId)
    {
        // Only allow deleting own entries (or super admin can delete any)
        if (!$this->isSuperAdmin && $this->viewingUserId !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $entry = CareerHistoryEntry::where('user_id', $this->viewingUserId)->findOrFail($entryId);
        $entry->delete();
        session()->flash('message', 'Career entry deleted successfully!');
    }

    public function getEntriesProperty()
    {
        return CareerHistoryEntry::where('user_id', $this->viewingUserId)
            ->orderBy('start_date', 'desc')
            ->orderBy('display_order', 'asc')
            ->get();
    }

    public function getDocumentsProperty()
    {
        return Document::where('user_id', $this->viewingUserId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTotalSeaServiceProperty()
    {
        $entries = $this->entries;
        $totalDays = 0;
        
        foreach ($entries as $entry) {
            if ($entry->qualifiesForSeaService()) {
                $totalDays += $entry->getSeaServiceDays();
            }
        }
        
        $years = floor($totalDays / 365);
        $months = floor(($totalDays % 365) / 30);
        
        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        if ($months > 0) {
            $parts[] = $months . ' ' . ($months === 1 ? 'month' : 'months');
        }
        
        return $parts ? implode(' ', $parts) : 'Less than 1 month';
    }

    public function getSummaryProperty()
    {
        $user = $this->viewingUser;
        return [
            'years_experience' => $user->years_experience ?? 0,
            'current_yacht' => $user->current_yacht ?? null,
            'current_yacht_start_date' => $user->current_yacht_start_date ?? null,
            'total_entries' => $this->entries->count(),
            'current_positions' => $this->entries->filter(fn($e) => $e->isCurrentPosition())->count(),
        ];
    }

    public function render()
    {
        return view('livewire.career-history.career-history-manager', [
            'entries' => $this->entries,
            'documents' => $this->documents,
            'totalSeaService' => $this->totalSeaService,
            'summary' => $this->summary,
            'isSuperAdmin' => $this->isSuperAdmin,
            'viewingUser' => $this->viewingUser,
            'canEdit' => $this->isSuperAdmin || $this->viewingUserId === Auth::id(),
            'users' => $this->users,
        ])->layout('layouts.app-laravel');
    }
}
