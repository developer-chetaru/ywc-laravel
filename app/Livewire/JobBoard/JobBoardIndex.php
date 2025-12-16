<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class JobBoardIndex extends Component
{
    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('super_admin');
        
        // Check if user is a verified captain/vessel OR has Captain role
        // Captains can post jobs, others need vessel verification
        $hasCaptainRole = $user->hasRole('Captain');
        $hasVerifiedVessel = $user->vesselVerification && $user->vesselVerification->isVerified();
        $isVerified = !$isAdmin && ($hasCaptainRole || $hasVerifiedVessel);
        
        // Get recent job posts (admins can see all, regular users see only published)
        $recentJobs = $isAdmin 
            ? \App\Models\JobPost::latest('created_at')->take(6)->get()
            : \App\Models\JobPost::published()->latest('published_at')->take(6)->get();
        
        // Admin stats
        if ($isAdmin) {
            $adminStats = [
                'pending_verifications' => \App\Models\VesselVerification::pending()->count(),
                'total_jobs' => \App\Models\JobPost::count(),
                'total_applications' => \App\Models\JobApplication::count(),
                'pending_bookings' => \App\Models\TemporaryWorkBooking::where('status', 'pending')->count(),
            ];
        } else {
            $adminStats = null;
            // Get user's active applications count (only for non-admins)
            $activeApplicationsCount = $user->jobApplications()
                ->whereIn('status', ['submitted', 'viewed', 'reviewed', 'shortlisted', 'interview_requested', 'interview_scheduled'])
                ->count();
            
            // Get user's job posts count (if captain)
            $myJobPostsCount = $user->jobPosts()->count();
            
            // Get pending payment bookings (for crew)
            $pendingPaymentCount = $user->temporaryWorkBookings()
                ->where('status', 'completed')
                ->where('payment_received', false)
                ->count();
        }
        
        return view('livewire.job-board.job-board-index', [
            'isVerified' => $isVerified,
            'isAdmin' => $isAdmin,
            'recentJobs' => $recentJobs,
            'activeApplicationsCount' => $activeApplicationsCount ?? 0,
            'myJobPostsCount' => $myJobPostsCount ?? 0,
            'pendingPaymentCount' => $pendingPaymentCount ?? 0,
            'adminStats' => $adminStats ?? null,
        ]);
    }
}
