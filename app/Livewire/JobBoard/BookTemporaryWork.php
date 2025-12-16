<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\JobPost;
use App\Models\TemporaryWorkBooking;
use App\Models\CrewAvailability;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class BookTemporaryWork extends Component
{
    public $crewUserId;
    public $crewUser;
    public $jobPostId;
    public $jobPost;
    
    public $workDate;
    public $startTime = '08:00';
    public $endTime = '18:00';
    public $totalHours = 10;
    public $dayRate;
    public $paymentMethod = 'cash';
    public $workDescription = '';
    public $location = '';
    public $berthDetails = '';

    public function mount($crewUserId, $jobPostId = null)
    {
        $this->crewUserId = $crewUserId;
        $this->jobPostId = $jobPostId;
        
        $this->crewUser = User::with('crewAvailability')->findOrFail($crewUserId);
        
        if ($jobPostId) {
            $this->jobPost = JobPost::findOrFail($jobPostId);
            $this->workDate = $this->jobPost->work_start_date?->format('Y-m-d');
            $this->startTime = $this->jobPost->work_start_time ? date('H:i', strtotime($this->jobPost->work_start_time)) : '08:00';
            $this->dayRate = $this->jobPost->day_rate_min ?? $this->crewUser->crewAvailability->day_rate_min ?? 150;
            $this->location = $this->jobPost->location ?? '';
            $this->workDescription = $this->jobPost->about_position ?? '';
        } else {
            $availability = $this->crewUser->crewAvailability;
            $this->dayRate = $availability->day_rate_min ?? 150;
            $this->location = Auth::user()->location_name ?? '';
        }

        // Verify user is a verified captain
        if (!Auth::user()->vesselVerification || !Auth::user()->vesselVerification->isVerified()) {
            abort(403, 'You must be a verified captain to book crew');
        }
    }

    public function updatedStartTime()
    {
        $this->calculateHours();
    }

    public function updatedEndTime()
    {
        $this->calculateHours();
    }

    public function calculateHours()
    {
        if ($this->startTime && $this->endTime) {
            $start = \Carbon\Carbon::parse($this->startTime);
            $end = \Carbon\Carbon::parse($this->endTime);
            $this->totalHours = $start->diffInHours($end);
        }
    }

    public function calculateTotal()
    {
        if (!$this->dayRate || !$this->totalHours) {
            return 0;
        }
        // Calculate based on day rate proportionally
        $hourlyRate = $this->dayRate / 10; // Assume 10 hour standard day
        return $this->totalHours * $hourlyRate;
    }

    public function confirmBooking()
    {
        $validated = $this->validate([
            'workDate' => 'required|date|after_or_equal:today',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime',
            'dayRate' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'workDescription' => 'required|string|max:500',
        ]);

        DB::transaction(function() {
            $totalPayment = $this->calculateTotal();

            // Create booking
            $booking = TemporaryWorkBooking::create([
                'job_post_id' => $this->jobPostId,
                'user_id' => $this->crewUserId,
                'booked_by_user_id' => Auth::id(),
                'status' => 'confirmed',
                'work_date' => $this->workDate,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'total_hours' => $this->totalHours,
                'work_description' => $this->workDescription,
                'location' => $this->location,
                'berth_details' => $this->berthDetails,
                'day_rate' => $this->dayRate,
                'total_payment' => $totalPayment,
                'payment_currency' => 'EUR',
                'payment_method' => $this->paymentMethod,
                'payment_timing' => 'End of day',
                'contact_name' => Auth::user()->name,
                'contact_phone' => Auth::user()->phone,
                'confirmed_at' => now(),
            ]);

            session()->flash('success', 'Booking confirmed! Crew member has been notified.');
            return redirect()->route('job-board.bookings');
        });
    }

    public function render()
    {
        return view('livewire.job-board.book-temporary-work');
    }
}

