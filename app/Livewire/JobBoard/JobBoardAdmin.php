<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\JobPost;
use App\Models\VesselVerification;
use App\Models\JobApplication;
use App\Models\TemporaryWorkBooking;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class JobBoardAdmin extends Component
{
    use WithPagination;

    public $activeTab = 'verifications'; // verifications, jobs, applications, bookings

    public function mount()
    {
        // Ensure user is admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Access denied. Admin only.');
        }
    }

    public function render()
    {
        $stats = [
            'pending_verifications' => VesselVerification::pending()->count(),
            'total_jobs' => JobPost::count(),
            'active_jobs' => JobPost::published()->count(),
            'total_applications' => JobApplication::count(),
            'pending_bookings' => TemporaryWorkBooking::where('status', 'pending')->count(),
        ];

        $verifications = VesselVerification::with(['user', 'yacht'])
            ->pending()
            ->latest()
            ->paginate(10, ['*'], 'verifications');

        $jobs = JobPost::with(['user', 'yacht'])
            ->latest()
            ->paginate(10, ['*'], 'jobs');

        $applications = JobApplication::with(['user', 'jobPost'])
            ->latest('submitted_at')
            ->paginate(10, ['*'], 'applications');

        $bookings = TemporaryWorkBooking::with(['user', 'jobPost', 'bookedBy'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'bookings');

        return view('livewire.job-board.job-board-admin', [
            'stats' => $stats,
            'verifications' => $verifications,
            'jobs' => $jobs,
            'applications' => $applications,
            'bookings' => $bookings,
        ]);
    }

    public function approveVerification($verificationId)
    {
        $verification = VesselVerification::findOrFail($verificationId);
        $verification->markAsVerified(Auth::id(), 'Approved by admin');
        
        session()->flash('success', 'Vessel verification approved.');
        $this->resetPage('verifications');
    }

    public function rejectVerification($verificationId, $reason = null)
    {
        $verification = VesselVerification::findOrFail($verificationId);
        $verification->update([
            'status' => 'rejected',
            'rejection_reason' => $reason ?? 'Rejected by admin',
            'reviewed_by_user_id' => Auth::id(),
        ]);
        
        session()->flash('success', 'Vessel verification rejected.');
        $this->resetPage('verifications');
    }

    public function deleteJob($jobId)
    {
        $job = JobPost::findOrFail($jobId);
        $job->delete();
        
        session()->flash('success', 'Job post deleted.');
        $this->resetPage('jobs');
    }

    public function toggleJobStatus($jobId)
    {
        $job = JobPost::findOrFail($jobId);
        $newStatus = $job->status === 'active' ? 'draft' : 'active';
        $job->update(['status' => $newStatus]);
        
        session()->flash('success', "Job status updated to {$newStatus}.");
        $this->resetPage('jobs');
    }
}

