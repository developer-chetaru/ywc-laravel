<?php

namespace App\Livewire\Training;

use Livewire\Component;
use App\Models\TrainingProviderCourse;
use Illuminate\Support\Facades\Auth;

class CourseDetail extends Component
{
    public $course;
    public $bookingUrl;
    public $isYwcMember = false;

    public function mount($courseId)
    {
        $user = Auth::user();
        $this->isYwcMember = $user && method_exists($user, 'hasActiveSubscription') && $user->hasActiveSubscription();

        $this->course = TrainingProviderCourse::with([
                'provider',
                'certification',
                'locations',
                'schedules' => function ($query) {
                    $query->where('is_cancelled', false)->orderBy('start_date');
                },
                'reviews.user',
            ])
            ->findOrFail($courseId);

        if ($this->course->booking_url) {
            if ($this->isYwcMember && $user) {
                $trackingCode = $this->course->ywc_tracking_code ?: 'YWC'.$user->id.'-'.$this->course->id;
                $separator = str_contains($this->course->booking_url, '?') ? '&' : '?';
                $this->bookingUrl = $this->course->booking_url.$separator.'ywc_code='.$trackingCode.'&discount='.$this->course->ywc_discount_percentage;
            } else {
                $this->bookingUrl = $this->course->booking_url;
            }
        }
    }

    public function render()
    {
        return view('livewire.training.course-detail', [
            'course'      => $this->course,
            'bookingUrl'  => $this->bookingUrl,
            'isYwcMember' => $this->isYwcMember,
        ])->layout('layouts.app');
    }
}


