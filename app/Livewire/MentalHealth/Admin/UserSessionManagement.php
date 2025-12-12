<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\MentalHealthSessionBooking;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserSessionManagement extends Component
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
    public $dateFilter = '';
    public $selectedSession = null;
    public $showDetails = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFilter' => ['except' => ''],
    ];

    public function viewSession($id)
    {
        $this->selectedSession = MentalHealthSessionBooking::with(['user', 'therapist.user'])->find($id);
        $this->showDetails = true;
    }

    public function cancelSession($id)
    {
        $session = MentalHealthSessionBooking::find($id);
        $session->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        
        session()->flash('message', 'Session cancelled successfully.');
        $this->showDetails = false;
    }

    public function render()
    {
        $query = MentalHealthSessionBooking::with(['user', 'therapist.user']);

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })->orWhereHas('therapist.user', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter) {
            if ($this->dateFilter === 'today') {
                $query->whereDate('scheduled_at', today());
            } elseif ($this->dateFilter === 'week') {
                $query->where('scheduled_at', '>=', Carbon::now()->startOfWeek())
                      ->where('scheduled_at', '<=', Carbon::now()->endOfWeek());
            } elseif ($this->dateFilter === 'month') {
                $query->whereMonth('scheduled_at', Carbon::now()->month)
                      ->whereYear('scheduled_at', Carbon::now()->year);
            }
        }

        $sessions = $query->orderBy('scheduled_at', 'desc')->paginate(20);

        $stats = [
            'total' => MentalHealthSessionBooking::count(),
            'confirmed' => MentalHealthSessionBooking::where('status', 'confirmed')->count(),
            'completed' => MentalHealthSessionBooking::where('status', 'completed')->count(),
            'cancelled' => MentalHealthSessionBooking::where('status', 'cancelled')->count(),
            'upcoming' => MentalHealthSessionBooking::where('status', 'confirmed')
                ->where('scheduled_at', '>=', now())->count(),
            'total_revenue' => MentalHealthSessionBooking::where('status', 'completed')
                ->sum('amount_paid'),
        ];

        return view('livewire.mental-health.admin.user-session-management', [
            'sessions' => $sessions,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
