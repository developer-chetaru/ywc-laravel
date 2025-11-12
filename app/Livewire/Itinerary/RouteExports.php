<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class RouteExports extends Component
{
    public ItineraryRoute $route;

    public function mount(ItineraryRoute $route): void
    {
        $this->route = $route;
    }

    public function render()
    {
        Gate::authorize('view', $this->route);

        return view('livewire.itinerary.route-exports');
    }
}

