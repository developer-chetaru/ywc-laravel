<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthSessionBooking;
use App\Models\MentalHealthMoodTracking;
use App\Models\MentalHealthGoal;
use App\Models\MentalHealthResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MentalHealthDashboard extends Component
{
    public $upcomingSessions;
    public $recentMoodEntries;
    public $activeGoals;
    public $creditBalance;
    public $recommendedResources;

    public function mount()
    {
        $user = Auth::user();
        
        // Get upcoming sessions
        $this->upcomingSessions = MentalHealthSessionBooking::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('scheduled_at', '>=', now())
            ->with('therapist.user')
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        // Get recent mood entries
        $this->recentMoodEntries = MentalHealthMoodTracking::where('user_id', $user->id)
            ->orderBy('tracked_date', 'desc')
            ->limit(7)
            ->get();

        // Get active goals
        $this->activeGoals = MentalHealthGoal::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Get credit balance
        $this->creditBalance = $user->mental_health_credit_balance ?? 0;

        // Get recommended resources (placeholder - implement recommendation logic later)
        $this->recommendedResources = MentalHealthResource::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.mental-health.mental-health-dashboard')->layout('layouts.app');
    }
}
