<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\JobPost;
use App\Models\SavedJobPost;
use App\Models\JobApplication;
use App\Models\TemporaryWorkBooking;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class JobDetail extends Component
{
    public $jobId;
    public $job;
    public $isSaved = false;
    public $hasApplied = false;
    public $application = null;

    public function mount($id)
    {
        $this->jobId = $id;
        $this->loadJob();
    }

    public function loadJob()
    {
        $this->job = JobPost::with(['user', 'yacht', 'screeningQuestions'])->findOrFail($this->jobId);
        
        // Check if saved
        if (Auth::check()) {
            $this->isSaved = SavedJobPost::where('user_id', Auth::id())
                ->where('job_post_id', $this->jobId)
                ->exists();
            
            // Check if applied
            if ($this->job->job_type === 'permanent') {
                $this->application = JobApplication::where('user_id', Auth::id())
                    ->where('job_post_id', $this->jobId)
                    ->first();
                
                $this->hasApplied = $this->application !== null;
            } else {
                // For temporary work, check if booked
                $this->application = TemporaryWorkBooking::where('user_id', Auth::id())
                    ->where('job_post_id', $this->jobId)
                    ->first();
                
                $this->hasApplied = $this->application !== null;
            }
        }

        // Increment views
        $this->job->incrementViews();
    }

    public function toggleSave()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isSaved) {
            SavedJobPost::where('user_id', Auth::id())
                ->where('job_post_id', $this->jobId)
                ->delete();
            $this->isSaved = false;
            $this->job->decrement('saved_count');
        } else {
            SavedJobPost::create([
                'user_id' => Auth::id(),
                'job_post_id' => $this->jobId,
            ]);
            $this->isSaved = true;
            $this->job->increment('saved_count');
        }
    }

    public function render()
    {
        return view('livewire.job-board.job-detail');
    }
}
