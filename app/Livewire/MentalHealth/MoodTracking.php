<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthMoodTracking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MoodTracking extends Component
{
    public $moodRating = 5;
    public $primaryMood = '';
    public $energyLevel = 5;
    public $sleepQuality = 5;
    public $stressLevel = 5;
    public $physicalSymptoms = [];
    public $triggerNotes = '';
    public $trackedDate;
    public $todayEntry = null;
    public $showCalendar = false;

    public function mount()
    {
        $this->trackedDate = today()->toDateString();
        $this->loadTodayEntry();
    }

    public function loadTodayEntry()
    {
        $this->todayEntry = MentalHealthMoodTracking::where('user_id', Auth::id())
            ->whereDate('tracked_date', $this->trackedDate)
            ->first();

        if ($this->todayEntry) {
            $this->moodRating = $this->todayEntry->mood_rating ?? 5;
            $this->primaryMood = $this->todayEntry->primary_mood ?? '';
            $this->energyLevel = $this->todayEntry->energy_level ?? 5;
            $this->sleepQuality = $this->todayEntry->sleep_quality ?? 5;
            $this->stressLevel = $this->todayEntry->stress_level ?? 5;
            $this->physicalSymptoms = $this->todayEntry->physical_symptoms ?? [];
            $this->triggerNotes = $this->todayEntry->trigger_notes ?? '';
        }
    }

    public function saveEntry()
    {
        $this->validate([
            'moodRating' => 'required|integer|min:1|max:10',
            'trackedDate' => 'required|date',
        ]);

        MentalHealthMoodTracking::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'tracked_date' => $this->trackedDate,
            ],
            [
                'mood_rating' => $this->moodRating,
                'primary_mood' => $this->primaryMood,
                'energy_level' => $this->energyLevel,
                'sleep_quality' => $this->sleepQuality,
                'stress_level' => $this->stressLevel,
                'physical_symptoms' => $this->physicalSymptoms,
                'trigger_notes' => $this->triggerNotes,
            ]
        );

        $this->loadTodayEntry();
        $this->dispatch('entry-saved');
    }

    public function updatedTrackedDate()
    {
        $this->loadTodayEntry();
    }

    public function render()
    {
        $recentEntries = MentalHealthMoodTracking::where('user_id', Auth::id())
            ->orderBy('tracked_date', 'desc')
            ->limit(30)
            ->get();

        // Calculate average mood for last 7 days
        $avgMood = MentalHealthMoodTracking::where('user_id', Auth::id())
            ->where('tracked_date', '>=', Carbon::now()->subDays(7))
            ->avg('mood_rating');

        return view('livewire.mental-health.mood-tracking', [
            'recentEntries' => $recentEntries,
            'avgMood' => round($avgMood ?? 5, 1),
        ])->layout('layouts.app');
    }
}
