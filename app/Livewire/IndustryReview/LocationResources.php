<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Yacht;
use App\Models\Marina;
use App\Models\Contractor;
use App\Models\Broker;
use App\Models\Restaurant;

#[Layout('layouts.app')]
class LocationResources extends Component
{
    public $location;
    public $yachts = [];
    public $marinas = [];
    public $contractors = [];
    public $brokers = [];
    public $restaurants = [];

    public function mount($location)
    {
        $this->location = $location;
        $this->loadResources();
    }

    public function loadResources()
    {
        // Load yachts based in this location
        $this->yachts = Yacht::where('home_port', 'like', "%{$this->location}%")
            ->orWhere('home_region', 'like', "%{$this->location}%")
            ->withCount('reviews')
            ->orderByDesc('rating_avg')
            ->limit(10)
            ->get();

        // Load marinas in this location
        $this->marinas = Marina::where('city', 'like', "%{$this->location}%")
            ->orWhere('country', 'like', "%{$this->location}%")
            ->withCount('reviews')
            ->orderByDesc('rating_avg')
            ->limit(10)
            ->get();

        // Load contractors in this location
        $this->contractors = Contractor::where('location', 'like', "%{$this->location}%")
            ->orWhere('city', 'like', "%{$this->location}%")
            ->orWhere('country', 'like', "%{$this->location}%")
            ->withCount('reviews')
            ->orderByDesc('rating_avg')
            ->limit(10)
            ->get();

        // Load brokers in this location
        $this->brokers = Broker::where('primary_location', 'like', "%{$this->location}%")
            ->withCount('reviews')
            ->orderByDesc('rating_avg')
            ->limit(10)
            ->get();

        // Load restaurants in this location
        $this->restaurants = Restaurant::where('city', 'like', "%{$this->location}%")
            ->orWhere('country', 'like', "%{$this->location}%")
            ->withCount('reviews')
            ->orderByDesc('rating_avg')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.industry-review.location-resources');
    }
}
