<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use App\Models\TrainingCertification;
use App\Models\TrainingProvider;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCourseReview;
use App\Models\TrainingUserCertification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrainingAdminDashboard extends Component
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
            'total_certifications' => TrainingCertification::count(),
            'active_certifications' => TrainingCertification::where('is_active', true)->count(),
            'pending_approvals' => TrainingCertification::where('requires_admin_approval', true)
                ->where('is_active', false)->count(),
            'total_providers' => TrainingProvider::count(),
            'active_providers' => TrainingProvider::where('is_active', true)->count(),
            'pending_providers' => TrainingProvider::where('is_active', false)->count(),
            'total_courses' => TrainingProviderCourse::count(),
            'active_courses' => TrainingProviderCourse::where('is_active', true)->count(),
            'pending_course_approvals' => TrainingProviderCourse::where('requires_admin_approval', true)
                ->where('is_active', false)->count(),
            'total_reviews' => TrainingCourseReview::count(),
            'pending_reviews' => TrainingCourseReview::where('is_approved', false)->count(),
            'total_user_certifications' => TrainingUserCertification::count(),
            'expiring_soon' => TrainingUserCertification::where('status', 'expiring_soon')->count(),
            'expired' => TrainingUserCertification::where('status', 'expired')->count(),
            'total_bookings' => TrainingProviderCourse::sum('booking_count'),
            'total_views' => TrainingProviderCourse::sum('view_count'),
        ];

        $recentCertifications = TrainingCertification::with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentProviders = TrainingProvider::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingApprovals = TrainingCertification::where('requires_admin_approval', true)
            ->where('is_active', false)
            ->with('category')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        $pendingCourseApprovals = TrainingProviderCourse::where('requires_admin_approval', true)
            ->where('is_active', false)
            ->with(['certification', 'provider'])
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        $topCourses = TrainingProviderCourse::with(['certification', 'provider'])
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.training.admin.training-admin-dashboard', [
            'stats' => $stats,
            'recentCertifications' => $recentCertifications,
            'recentProviders' => $recentProviders,
            'pendingApprovals' => $pendingApprovals,
            'pendingCourseApprovals' => $pendingCourseApprovals,
            'topCourses' => $topCourses,
        ])->layout('layouts.app');
    }
}
