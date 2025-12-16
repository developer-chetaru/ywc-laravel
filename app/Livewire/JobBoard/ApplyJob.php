<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Services\JobBoard\JobMatchingService;
use App\Services\JobBoard\JobNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class ApplyJob extends Component
{
    public $jobId;
    public $job;
    public $screeningResponses = [];
    public $coverMessage = '';
    public $attachedDocuments = [];

    public function mount($id)
    {
        // Block admins from applying
        if (Auth::user()->hasRole('super_admin')) {
            abort(403, 'Admins cannot apply for jobs. Use the admin panel to manage jobs.');
        }

        $this->jobId = $id;
        $this->job = JobPost::with('screeningQuestions')->findOrFail($id);

        // Redirect temporary work applications to the correct route
        if ($this->job->job_type === 'temporary') {
            return redirect()->route('job-board.apply-temporary', $this->jobId);
        }

        // Check if already applied
        $existing = JobApplication::where('user_id', Auth::id())
            ->where('job_post_id', $this->jobId)
            ->first();

        if ($existing) {
            return redirect()->route('job-board.detail', $this->jobId)
                ->with('info', 'You have already applied for this position.');
        }

        // Initialize screening responses
        foreach ($this->job->screeningQuestions as $question) {
            $this->screeningResponses[$question->id] = '';
        }
    }

    public function submitApplication()
    {
        $this->validate([
            'coverMessage' => 'nullable|string|max:2000',
            'screeningResponses.*' => 'required_if:is_required,true',
        ]);

        DB::transaction(function() {
            // Calculate match score
            $matchingService = app(JobMatchingService::class);
            $crew = Auth::user();
            $matchScore = $matchingService->calculateMatchScore($crew, $this->job);

            // Create application
            $application = JobApplication::create([
                'job_post_id' => $this->jobId,
                'user_id' => Auth::id(),
                'status' => 'submitted',
                'match_score' => $matchScore,
                'screening_responses' => $this->screeningResponses,
                'cover_message' => $this->coverMessage,
                'submitted_at' => now(),
            ]);

            // Update job post application count
            $this->job->incrementApplications();

            // Notify captain
            app(JobNotificationService::class)->notifyNewApplication($application);

            session()->flash('success', 'Application submitted successfully!');
            return redirect()->route('job-board.my-applications');
        });
    }

    public function render()
    {
        return view('livewire.job-board.apply-job');
    }
}
