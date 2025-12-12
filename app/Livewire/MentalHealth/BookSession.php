<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthTherapist;
use App\Models\MentalHealthSessionBooking;
use App\Models\MentalHealthTherapistAvailability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookSession extends Component
{
    public $therapistId = null;
    public $selectedTherapist = null;
    public $sessionType = 'video';
    public $duration = 60;
    public $selectedDate = null;
    public $selectedTime = null;
    public $availableSlots = [];
    public $creditsToUse = 0;
    public $totalCost = 0;
    public $step = 1;

    public function mount($therapistId = null)
    {
        if ($therapistId) {
            $this->therapistId = $therapistId;
            $this->selectTherapist($therapistId);
        } elseif (request()->has('therapist')) {
            $this->therapistId = request()->get('therapist');
            $this->selectTherapist($this->therapistId);
        }
    }

    public function selectTherapist($id)
    {
        $this->therapistId = $id;
        $this->selectedTherapist = MentalHealthTherapist::where('id', $id)
            ->where('application_status', 'approved')
            ->where('is_active', true)
            ->with('user')
            ->first();
        
        if ($this->selectedTherapist) {
            $this->step = 2;
            $this->calculateCost();
        }
    }

    public function updatedSessionType()
    {
        $this->calculateCost();
    }

    public function updatedDuration()
    {
        $this->calculateCost();
        if ($this->selectedDate) {
            $this->loadAvailableSlots();
        }
    }

    public function updatedSelectedDate()
    {
        $this->selectedTime = null;
        $this->loadAvailableSlots();
    }

    public function calculateCost()
    {
        if (!$this->selectedTherapist) return;

        $baseRate = $this->selectedTherapist->base_hourly_rate ?? 0;
        $hourlyMultiplier = $this->duration / 60;
        
        // Get session type pricing if available
        $sessionPricing = $this->selectedTherapist->session_type_pricing ?? [];
        $typeMultiplier = isset($sessionPricing[$this->sessionType]) 
            ? ($sessionPricing[$this->sessionType] / 100) 
            : 1;

        $this->totalCost = $baseRate * $hourlyMultiplier * $typeMultiplier;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableSlots();
    }

    public function loadAvailableSlots()
    {
        if (!$this->selectedDate || !$this->selectedTherapist) {
            $this->availableSlots = [];
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        $dayOfWeek = strtolower($date->format('l'));

        // Get therapist availability for this day
        $availability = MentalHealthTherapistAvailability::where('therapist_id', $this->selectedTherapist->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->first();

        if ($availability) {
            // Generate time slots
            $start = Carbon::parse($availability->start_time);
            $end = Carbon::parse($availability->end_time);
            $slots = [];

            while ($start->copy()->addMinutes($this->duration)->lte($end)) {
                $slotTime = $start->format('H:i');
                
                // Check if slot is already booked
                $isBooked = MentalHealthSessionBooking::where('therapist_id', $this->selectedTherapist->id)
                    ->whereDate('scheduled_at', $date)
                    ->whereTime('scheduled_at', $slotTime)
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->exists();

                if (!$isBooked && $start->copy()->setDate($date->year, $date->month, $date->day)->isFuture()) {
                    $slots[] = $slotTime;
                }

                $start->addMinutes($this->duration + ($availability->buffer_minutes ?? 15));
            }

            $this->availableSlots = $slots;
        } else {
            $this->availableSlots = [];
        }
    }

    public function selectTime($time)
    {
        $this->selectedTime = $time;
        $this->step = 3;
    }

    public function confirmBooking()
    {
        $this->validate([
            'selectedTherapist' => 'required',
            'sessionType' => 'required',
            'duration' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
        ]);

        $scheduledAt = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);

        $booking = MentalHealthSessionBooking::create([
            'user_id' => Auth::id(),
            'therapist_id' => $this->selectedTherapist->id,
            'session_type' => $this->sessionType,
            'duration_minutes' => $this->duration,
            'scheduled_at' => $scheduledAt,
            'timezone' => config('app.timezone'),
            'status' => 'pending',
            'session_cost' => $this->totalCost,
            'credits_used' => min($this->creditsToUse, $this->totalCost),
            'amount_paid' => max(0, $this->totalCost - $this->creditsToUse),
        ]);

        return redirect()->route('mental-health.sessions')->with('success', 'Session booked successfully!');
    }

    public function render()
    {
        $therapists = MentalHealthTherapist::where('application_status', 'approved')
            ->where('is_active', true)
            ->with('user')
            ->get();

        $user = Auth::user();
        $availableCredits = $user->mental_health_credit_balance ?? 0;

        return view('livewire.mental-health.book-session', [
            'therapists' => $therapists,
            'availableCredits' => $availableCredits,
        ])->layout('layouts.app');
    }
}
