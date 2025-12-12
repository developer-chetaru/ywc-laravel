<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthTherapistCredential;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TherapistManagement extends Component
{
    use WithPagination;

    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public $search = '';
    public $statusFilter = 'all';
    public $selectedTherapist = null;
    public $showDetails = false;
    public $showCredentials = false;
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];


    public function viewTherapist($id)
    {
        $this->selectedTherapist = MentalHealthTherapist::with(['user', 'credentials', 'availability'])->find($id);
        $this->showDetails = true;
    }

    public function approveTherapist($id)
    {
        $therapist = MentalHealthTherapist::find($id);
        $therapist->update([
            'application_status' => 'approved',
            'is_active' => true,
        ]);
        
        session()->flash('message', 'Therapist approved successfully.');
        $this->showDetails = false;
    }

    public function rejectTherapist($id, $reason = null)
    {
        $therapist = MentalHealthTherapist::find($id);
        $therapist->update([
            'application_status' => 'rejected',
            'rejection_reason' => $reason ?? 'Application did not meet requirements',
            'is_active' => false,
        ]);
        
        session()->flash('message', 'Therapist application rejected.');
        $this->showDetails = false;
    }

    public function toggleActive($id)
    {
        $therapist = MentalHealthTherapist::find($id);
        $therapist->update([
            'is_active' => !$therapist->is_active,
        ]);
        
        session()->flash('message', 'Therapist status updated.');
    }

    public function render()
    {
        $query = MentalHealthTherapist::with('user');

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('application_status', $this->statusFilter);
        }

        $therapists = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => MentalHealthTherapist::count(),
            'approved' => MentalHealthTherapist::where('application_status', 'approved')->count(),
            'pending' => MentalHealthTherapist::where('application_status', 'pending')->count(),
            'rejected' => MentalHealthTherapist::where('application_status', 'rejected')->count(),
            'active' => MentalHealthTherapist::where('is_active', true)->count(),
        ];

        return view('livewire.mental-health.admin.therapist-management', [
            'therapists' => $therapists,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
