<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthTherapist;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class TherapistDirectory extends Component
{
    use WithPagination;

    public $search = '';
    public $specialization = '';
    public $language = '';
    public $sessionType = '';
    public $priceRange = [0, 1000];
    public $sortBy = 'relevance';
    public $availability = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'specialization' => ['except' => ''],
        'language' => ['except' => ''],
        'sessionType' => ['except' => ''],
        'sortBy' => ['except' => 'relevance'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = MentalHealthTherapist::where('application_status', 'approved')
            ->where('is_active', true)
            ->with('user');

        // Search
        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('biography', 'like', '%' . $this->search . '%')
            ->orWhereJsonContains('specializations', $this->search);
        }

        // Filters
        if ($this->specialization) {
            $query->whereJsonContains('specializations', $this->specialization);
        }

        if ($this->language) {
            $query->whereJsonContains('languages_spoken', $this->language);
        }

        if ($this->priceRange[1] < 1000) {
            $query->where('base_hourly_rate', '<=', $this->priceRange[1])
                  ->where('base_hourly_rate', '>=', $this->priceRange[0]);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'price_low':
                $query->orderBy('base_hourly_rate', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_hourly_rate', 'desc');
                break;
            case 'experience':
                $query->orderBy('years_experience', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('rating', 'desc');
        }

        $therapists = $query->paginate(12);

        return view('livewire.mental-health.therapist-directory', [
            'therapists' => $therapists,
        ])->layout('layouts.app');
    }

    public function addToFavorites($therapistId)
    {
        $user = Auth::user();
        \App\Models\MentalHealthFavorite::firstOrCreate([
            'user_id' => $user->id,
            'favorite_type' => 'therapist',
            'favorite_id' => $therapistId,
        ]);

        $this->dispatch('favorite-added');
    }
}
