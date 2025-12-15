<?php

namespace App\Livewire\Training;

use Livewire\Component;
use App\Models\TrainingCourseSchedule;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingUserCertification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CourseScheduleCalendar extends Component
{
    public $view = 'month'; // month, week, day
    public $currentDate;
    public $schedules = [];
    public $userCertifications = [];
    public $providerId = null; // For provider portal
    public $courseId = null; // For specific course

    public function mount($provider = null, $courseId = null)
    {
        $this->providerId = $provider;
        $this->courseId = $courseId;
        $this->currentDate = Carbon::now();
        $this->loadSchedules();
        $this->loadUserCertifications();
    }

    public function loadSchedules()
    {
        $query = TrainingCourseSchedule::with(['providerCourse.certification', 'providerCourse.provider', 'location'])
            ->where('is_cancelled', false)
            ->where('start_date', '>=', $this->currentDate->copy()->startOfMonth())
            ->where('start_date', '<=', $this->currentDate->copy()->endOfMonth()->addMonths(2));

        if ($this->providerId) {
            $query->whereHas('providerCourse', function ($q) {
                $q->where('provider_id', $this->providerId);
            });
        }

        if ($this->courseId) {
            $query->where('provider_course_id', $this->courseId);
        }

        $this->schedules = $query->orderBy('start_date')->get();
    }

    public function loadUserCertifications()
    {
        if (Auth::check()) {
            $this->userCertifications = TrainingUserCertification::where('user_id', Auth::id())
                ->with('certification')
                ->get();
        }
    }

    public function previousMonth()
    {
        $this->currentDate->subMonth();
        $this->loadSchedules();
    }

    public function nextMonth()
    {
        $this->currentDate->addMonth();
        $this->loadSchedules();
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::now();
        $this->loadSchedules();
    }

    public function getSchedulesForDate($date)
    {
        return $this->schedules->filter(function ($schedule) use ($date) {
            return Carbon::parse($schedule->start_date)->isSameDay($date);
        });
    }

    public function render()
    {
        $calendarDays = $this->buildCalendar();
        
        return view('livewire.training.course-schedule-calendar', [
            'calendarDays' => $calendarDays,
        ])->layout('layouts.app');
    }

    private function buildCalendar()
    {
        $start = $this->currentDate->copy()->startOfMonth()->startOfWeek();
        $end = $this->currentDate->copy()->endOfMonth()->endOfWeek();
        $days = [];

        $current = $start->copy();
        while ($current <= $end) {
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $this->currentDate->month,
                'isToday' => $current->isToday(),
                'schedules' => $this->getSchedulesForDate($current),
            ];
            $current->addDay();
        }

        return $days;
    }
}
