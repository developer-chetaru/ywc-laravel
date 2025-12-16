<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CrewAvailability;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CrewAvailabilitySettings extends Component
{
    public $status = 'not_available';
    public $availableFrom = '';
    public $availableUntil = '';
    public $noticeRequired = 'immediate';
    
    public $dayWork = false;
    public $shortContracts = false;
    public $mediumContracts = false;
    public $emergencyCover = false;
    public $longTermSeasonal = false;
    
    public $availablePositions = [];
    public $dayRateMin = '';
    public $dayRateMax = '';
    public $halfDayRate = '';
    public $emergencyRate = '';
    public $weeklyContractRate = '';
    public $ratesNegotiable = true;
    
    public $blockedDates = [];
    public $currentLocation = '';
    public $latitude = '';
    public $longitude = '';
    public $searchRadiusKm = 20;
    public $autoUpdateLocation = true;
    
    public $notifySameDayUrgent = true;
    public $notify24HourJobs = true;
    public $notify3DayJobs = true;
    public $notifyWeeklyContracts = true;
    public $alertFrequency = 'immediately';
    public $quietHoursStart = '';
    public $quietHoursEnd = '';
    
    public $profileVisibility = 'all_verified';
    public $showRatings = true;
    public $showLastWorkedDate = true;
    public $showJobCount = true;
    public $showResponseTime = true;
    public $showCurrentVessel = false;
    public $showFullExperience = false;
    public $allowDirectBooking = false;

    public $availability = null;

    public function mount()
    {
        $this->availability = Auth::user()->crewAvailability;
        
        if ($this->availability) {
            $this->loadAvailability();
        }
    }

    public function loadAvailability()
    {
        $a = $this->availability;
        $this->status = $a->status;
        $this->availableFrom = $a->available_from?->format('Y-m-d');
        $this->availableUntil = $a->available_until?->format('Y-m-d');
        $this->noticeRequired = $a->notice_required ?? 'immediate';
        $this->dayWork = $a->day_work;
        $this->shortContracts = $a->short_contracts;
        $this->mediumContracts = $a->medium_contracts;
        $this->emergencyCover = $a->emergency_cover;
        $this->availablePositions = $a->available_positions ?? [];
        $this->dayRateMin = $a->day_rate_min;
        $this->dayRateMax = $a->day_rate_max;
        $this->searchRadiusKm = $a->search_radius_km ?? 20;
        // ... load all fields
    }

    public function save()
    {
        $validated = $this->validate([
            'status' => 'required|in:available_now,available_with_notice,not_available',
            'dayRateMin' => 'nullable|numeric|min:0',
            'dayRateMax' => 'nullable|numeric|min:0|gte:dayRateMin',
            'searchRadiusKm' => 'required|integer|min:1|max:500',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'status' => $this->status,
            'available_from' => $this->availableFrom ?: null,
            'available_until' => $this->availableUntil ?: null,
            'notice_required' => $this->noticeRequired,
            'day_work' => $this->dayWork,
            'short_contracts' => $this->shortContracts,
            'medium_contracts' => $this->mediumContracts,
            'emergency_cover' => $this->emergencyCover,
            'available_positions' => $this->availablePositions,
            'day_rate_min' => $this->dayRateMin ?: null,
            'day_rate_max' => $this->dayRateMax ?: null,
            'rates_negotiable' => $this->ratesNegotiable,
            'search_radius_km' => $this->searchRadiusKm,
            'auto_update_location' => $this->autoUpdateLocation,
            'notify_same_day_urgent' => $this->notifySameDayUrgent,
            'notify_24_hour_jobs' => $this->notify24HourJobs,
            'profile_visibility' => $this->profileVisibility,
            // ... all other fields
        ];

        if ($this->availability) {
            $this->availability->update($data);
        } else {
            CrewAvailability::create($data);
        }

        session()->flash('success', 'Availability settings saved!');
    }

    public function toggleAvailability()
    {
        if ($this->status === 'available_now') {
            $this->status = 'not_available';
        } else {
            $this->status = 'available_now';
        }
        $this->save();
    }

    public function render()
    {
        return view('livewire.job-board.crew-availability-settings');
    }
}
