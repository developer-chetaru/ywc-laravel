<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthSessionBooking;
use App\Models\MentalHealthResource;
use App\Models\MentalHealthCrisisSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Component
{
    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function render()
    {
        $stats = [
            'total_therapists' => MentalHealthTherapist::count(),
            'active_therapists' => MentalHealthTherapist::where('is_active', true)->count(),
            'pending_applications' => MentalHealthTherapist::where('application_status', 'pending')->count(),
            'total_sessions' => MentalHealthSessionBooking::count(),
            'completed_sessions' => MentalHealthSessionBooking::where('status', 'completed')->count(),
            'upcoming_sessions' => MentalHealthSessionBooking::where('status', 'confirmed')
                ->where('scheduled_at', '>=', now())->count(),
            'total_resources' => MentalHealthResource::where('status', 'published')->count(),
            'crisis_sessions_today' => MentalHealthCrisisSession::whereDate('created_at', today())->count(),
            'crisis_sessions_week' => MentalHealthCrisisSession::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'active_users' => User::whereHas('mentalHealthSessionBookings')->distinct()->count(),
        ];

        $recentTherapists = MentalHealthTherapist::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentSessions = MentalHealthSessionBooking::with(['user', 'therapist.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingApplications = MentalHealthTherapist::with('user')
            ->where('application_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        return view('livewire.mental-health.admin.admin-dashboard', [
            'stats' => $stats,
            'recentTherapists' => $recentTherapists,
            'recentSessions' => $recentSessions,
            'pendingApplications' => $pendingApplications,
        ])->layout('layouts.app');
    }
}
