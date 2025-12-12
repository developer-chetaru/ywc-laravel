<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\MentalHealthSessionBooking;
use App\Models\MentalHealthMoodTracking;
use App\Models\MentalHealthGoal;
use App\Models\MentalHealthJournal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserAnalytics extends Component
{
    public $timeRange = 'month'; // week, month, year

    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function render()
    {
        $startDate = match($this->timeRange) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        // User engagement stats
        $activeUsers = User::whereHas('mentalHealthSessionBookings', function($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        })->orWhereHas('mentalHealthMoodTracking', function($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        })->distinct()->count();

        $totalUsers = User::whereHas('mentalHealthSessionBookings')
            ->orWhereHas('mentalHealthMoodTracking')
            ->distinct()->count();

        // Session statistics
        $sessionsData = MentalHealthSessionBooking::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Mood tracking statistics
        $moodData = MentalHealthMoodTracking::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(tracked_date) as date, AVG(mood_rating) as avg_mood')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Goal completion rate
        $totalGoals = MentalHealthGoal::where('created_at', '>=', $startDate)->count();
        $completedGoals = MentalHealthGoal::where('created_at', '>=', $startDate)
            ->where('status', 'completed')->count();
        $completionRate = $totalGoals > 0 ? ($completedGoals / $totalGoals) * 100 : 0;

        // Top therapists by sessions
        $topTherapists = MentalHealthSessionBooking::where('created_at', '>=', $startDate)
            ->selectRaw('therapist_id, COUNT(*) as session_count')
            ->groupBy('therapist_id')
            ->orderBy('session_count', 'desc')
            ->limit(5)
            ->with('therapist.user')
            ->get();

        // Most popular resources
        $popularResources = \App\Models\MentalHealthResource::where('created_at', '>=', $startDate)
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'active_users' => $activeUsers,
            'total_users' => $totalUsers,
            'total_sessions' => MentalHealthSessionBooking::where('created_at', '>=', $startDate)->count(),
            'completed_sessions' => MentalHealthSessionBooking::where('created_at', '>=', $startDate)
                ->where('status', 'completed')->count(),
            'total_mood_entries' => MentalHealthMoodTracking::where('created_at', '>=', $startDate)->count(),
            'average_mood' => MentalHealthMoodTracking::where('created_at', '>=', $startDate)
                ->avg('mood_rating'),
            'goal_completion_rate' => round($completionRate, 1),
            'total_revenue' => MentalHealthSessionBooking::where('created_at', '>=', $startDate)
                ->where('status', 'completed')
                ->sum('amount_paid'),
        ];

        return view('livewire.mental-health.admin.user-analytics', [
            'stats' => $stats,
            'sessionsData' => $sessionsData,
            'moodData' => $moodData,
            'topTherapists' => $topTherapists,
            'popularResources' => $popularResources,
        ])->layout('layouts.app');
    }
}
