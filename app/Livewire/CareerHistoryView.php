<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Yacht;
use App\Models\User;
use Carbon\Carbon;

#[Layout('layouts.app')]
class CareerHistoryView extends Component
{
    use WithPagination;

    public $selectedUserId = null;
    public $user;
    public $years_experience;
    public $current_yacht;
    public $current_yacht_start_date;
    public $previous_yachts = [];
    public $yachts = [];
    
    // For super admin user search
    public $search = '';
    public $isSuperAdmin = false;

    public function mount($userId = null)
    {
        $currentUser = Auth::user();
        $this->isSuperAdmin = $currentUser->hasRole('super_admin');
        
        // If super admin and userId provided, use that user; otherwise use current user
        if ($this->isSuperAdmin && $userId) {
            $this->selectedUserId = $userId;
            $this->user = User::findOrFail($userId);
        } else {
            $this->user = $currentUser;
        }
        
        $this->loadUserData();
    }
    
    public function loadUserData()
    {
        if (!$this->user) {
            return;
        }
        
        $this->years_experience = $this->user->years_experience;
        $this->current_yacht = $this->user->current_yacht;
        $this->current_yacht_start_date = $this->user->current_yacht_start_date;
        
        // Load previous yachts - handle both old format (strings) and new format (objects)
        $previousYachts = $this->user->previous_yachts ?? [];
        $this->previous_yachts = [];
        foreach ($previousYachts as $yacht) {
            if (is_string($yacht)) {
                // Old format - convert to new format
                $this->previous_yachts[] = [
                    'yacht_id' => null,
                    'name' => $yacht,
                    'start_date' => null,
                    'end_date' => null,
                ];
            } else {
                // New format - fix invalid dates (swap if end < start)
                $startDate = !empty($yacht['start_date']) ? $yacht['start_date'] : null;
                $endDate = !empty($yacht['end_date']) ? $yacht['end_date'] : null;
                
                // Fix invalid dates by swapping if end date is before start date
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);
                    
                    if ($end->lt($start)) {
                        // Swap dates if end is before start
                        $temp = $startDate;
                        $startDate = $endDate;
                        $endDate = $temp;
                    }
                }
                
                $this->previous_yachts[] = [
                    'yacht_id' => $yacht['yacht_id'] ?? null,
                    'name' => $yacht['name'] ?? '',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ];
            }
        }
        
        // Load yachts for display (to get yacht details if yacht_id exists)
        $this->yachts = Yacht::all()->keyBy('id');
    }
    
    public function selectUser($userId)
    {
        $this->selectedUserId = $userId;
        $this->user = User::findOrFail($userId);
        $this->loadUserData();
        $this->resetPage();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = collect();
        
        // For super admin, show user list for selection
        if ($this->isSuperAdmin) {
            $query = User::query()
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
            
            $users = $query->orderBy('first_name')->orderBy('last_name')->paginate(10);
        }
        
        return view('livewire.career-history-view', [
            'users' => $users,
        ]);
    }
}

