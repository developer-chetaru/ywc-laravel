<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthCrisisSession;
use App\Models\MentalHealthTherapist;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CrisisSupport extends Component
{
    public $step = 1;
    public $severity = '';
    public $location = '';
    public $assessmentAnswers = [];
    public $sessionType = 'chat';
    public $connecting = false;
    public $crisisSession = null;

    public function mount()
    {
        // Check if user has active crisis session
        $this->crisisSession = MentalHealthCrisisSession::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();
        
        if ($this->crisisSession) {
            $this->step = 3;
            $this->connecting = true;
        }
    }

    public function startAssessment()
    {
        $this->step = 2;
    }

    public function submitAssessment()
    {
        $this->validate([
            'severity' => 'required',
        ]);

        // Create crisis session
        $this->crisisSession = MentalHealthCrisisSession::create([
            'user_id' => Auth::id(),
            'severity_level' => $this->severity,
            'location' => $this->location,
            'assessment_answers' => $this->assessmentAnswers,
            'session_type' => $this->sessionType,
            'status' => 'pending',
        ]);

        $this->step = 3;
        $this->connectToCounselor();
    }

    public function connectToCounselor()
    {
        $this->connecting = true;
        
        // Find available crisis counselor (therapist marked for crisis support)
        $counselor = MentalHealthTherapist::where('is_active', true)
            ->where('application_status', 'approved')
            ->first(); // In real implementation, filter for crisis counselors

        if ($counselor) {
            $this->crisisSession->update([
                'counselor_id' => $counselor->id,
                'status' => 'in_progress',
                'connected_at' => now(),
            ]);
        }

        // In production, this would connect via WebRTC or chat system
    }

    public function render()
    {
        return view('livewire.mental-health.crisis-support')->layout('layouts.app');
    }
}
