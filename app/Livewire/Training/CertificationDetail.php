<?php

namespace App\Livewire\Training;

use Livewire\Component;
use App\Models\TrainingCertification;
use App\Models\TrainingProviderCourse;
use Illuminate\Support\Facades\Auth;

class CertificationDetail extends Component
{
    public $certification;
    public $certificationSlug;
    public $selectedProviderCourse = null;

    public function mount($slug)
    {
        $this->certificationSlug = $slug;
        $this->certification = TrainingCertification::where('slug', $slug)
            ->with(['category', 'providerCourses.provider', 'providerCourses.locations', 'providerCourses.upcomingSchedules'])
            ->where('is_active', true)
            ->firstOrFail();

        // Increment view count (could be moved to a service)
        $this->certification->increment('provider_count');
    }

    public function selectProvider($courseId)
    {
        $this->selectedProviderCourse = TrainingProviderCourse::with([
            'provider',
            'certification',
            'locations',
            'schedules' => function ($query) {
                $query->where('start_date', '>=', now())
                    ->where('is_cancelled', false)
                    ->orderBy('start_date');
            },
            'reviews.user'
        ])->findOrFail($courseId);
    }

    public function closeProviderDetail()
    {
        $this->selectedProviderCourse = null;
    }

    public function generateBookingUrl($courseId)
    {
        $user = Auth::user();
        $course = TrainingProviderCourse::findOrFail($courseId);
        
        // Generate YWC tracking code if user is a member
        $isYwcMember = $user && $user->hasActiveSubscription();
        
        if ($isYwcMember && $course->booking_url) {
            $trackingCode = $course->ywc_tracking_code ?: 'YWC' . $user->id . '-' . $courseId;
            $separator = strpos($course->booking_url, '?') !== false ? '&' : '?';
            return $course->booking_url . $separator . 'ywc_code=' . $trackingCode . '&discount=' . $course->ywc_discount_percentage;
        }
        
        return $course->booking_url;
    }

    public function render()
    {
        $user = Auth::user();
        $isYwcMember = $user && $user->hasActiveSubscription();

        // Get active provider courses for this certification
        $providerCourses = $this->certification->activeProviderCourses()
            ->with(['provider', 'locations', 'upcomingSchedules'])
            ->orderBy('rating_avg', 'desc')
            ->orderBy('price', 'asc')
            ->get();

        return view('livewire.training.certification-detail', [
            'providerCourses' => $providerCourses,
            'isYwcMember' => $isYwcMember,
        ])->layout('layouts.app');
    }
}
