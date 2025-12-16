<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Services\JobBoard\JobNotificationService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ManageApplications extends Component
{
    use WithPagination;

    #[Url]
    public $filter = 'all'; // all, new, reviewed, shortlisted, interviewed, offers, declined

    #[Url]
    public $jobPostId = '';

    public $viewMode = 'captain'; // captain or crew

    public function mount()
    {
        // Block admins from accessing user applications
        if (Auth::user()->hasRole('super_admin')) {
            return redirect()->route('job-board.admin');
        }

        // Determine view mode based on Captain role or vessel verification
        $user = Auth::user();
        $hasCaptainRole = $user->hasRole('Captain');
        $hasVerifiedVessel = $user->vesselVerification && $user->vesselVerification->isVerified();
        $this->viewMode = ($hasCaptainRole || $hasVerifiedVessel) ? 'captain' : 'crew';
    }

    public function render()
    {
        if ($this->viewMode === 'captain') {
            return $this->renderCaptainView();
        } else {
            return $this->renderCrewView();
        }
    }

    private function renderCaptainView()
    {
        $query = JobApplication::with(['user', 'jobPost'])
            ->whereHas('jobPost', function($q) {
                $q->where('user_id', Auth::id());
            });

        if ($this->jobPostId) {
            $query->where('job_post_id', $this->jobPostId);
        }

        if ($this->filter !== 'all') {
            switch ($this->filter) {
                case 'new':
                    $query->where('status', 'submitted');
                    break;
                case 'reviewed':
                    $query->where('status', 'reviewed');
                    break;
                case 'shortlisted':
                    $query->where('status', 'shortlisted');
                    break;
                case 'interviewed':
                    $query->whereIn('status', ['interview_requested', 'interview_scheduled', 'interviewed']);
                    break;
                case 'offers':
                    $query->whereIn('status', ['offer_sent', 'offer_accepted', 'offer_declined']);
                    break;
                case 'declined':
                    $query->where('status', 'declined');
                    break;
            }
        }

        $applications = $query->orderBy('match_score', 'desc')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        $jobPosts = JobPost::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('livewire.job-board.manage-applications', [
            'applications' => $applications,
            'jobPosts' => $jobPosts,
            'viewMode' => 'captain',
        ]);
    }

    private function renderCrewView()
    {
        $query = JobApplication::with('jobPost')
            ->where('user_id', Auth::id());

        if ($this->filter !== 'all') {
            switch ($this->filter) {
                case 'active':
                    $query->whereIn('status', ['submitted', 'viewed', 'reviewed', 'shortlisted', 'interview_requested', 'interview_scheduled']);
                    break;
                case 'interviewing':
                    $query->whereIn('status', ['interview_requested', 'interview_scheduled', 'interviewed']);
                    break;
                case 'offers':
                    $query->whereIn('status', ['offer_sent', 'offer_accepted', 'offer_declined']);
                    break;
                case 'closed':
                    $query->whereIn('status', ['declined', 'withdrawn', 'hired']);
                    break;
            }
        }

        $applications = $query->orderBy('submitted_at', 'desc')
            ->paginate(20);

        return view('livewire.job-board.manage-applications', [
            'applications' => $applications,
            'jobPosts' => collect(),
            'viewMode' => 'crew',
        ]);
    }

    public function updateStatus($applicationId, $status, $notes = null)
    {
        $application = JobApplication::findOrFail($applicationId);
        
        // Verify ownership
        if ($application->jobPost->user_id !== Auth::id()) {
            abort(403);
        }

        $application->update([
            'status' => $status,
            'captain_notes' => $notes,
        ]);

        // Update status timestamps
        switch ($status) {
            case 'reviewed':
                $application->markAsReviewed();
                break;
            case 'shortlisted':
                $application->shortlist();
                break;
        }

        // Notify crew
        app(JobNotificationService::class)->notifyApplicationStatusChange($application);

        session()->flash('success', 'Application status updated');
    }

    public function withdraw($applicationId, $reason = null)
    {
        $application = JobApplication::findOrFail($applicationId);
        
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->withdraw($reason);
        session()->flash('success', 'Application withdrawn');
    }
}
