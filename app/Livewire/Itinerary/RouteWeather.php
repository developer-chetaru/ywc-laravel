<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Services\Itinerary\WeatherService;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class RouteWeather extends Component
{
    public ItineraryRoute $route;
    public bool $syncing = false;
    public ?string $message = null;

    public function mount(ItineraryRoute $route): void
    {
        $this->route = $route->load(['stops.weatherSnapshots']);
    }

    public function syncWeather(): void
    {
        Gate::authorize('view', $this->route);

        $this->syncing = true;
        $this->message = null;

        try {
            $service = app(WeatherService::class);
            $service->syncRouteWeather($this->route, 7);
            $this->message = 'Weather data refreshed successfully.';
            $this->route->refresh();
            $this->route->load(['stops.weatherSnapshots']);
        } catch (\Exception $e) {
            $this->message = 'Failed to sync weather: ' . $e->getMessage();
        } finally {
            $this->syncing = false;
        }
    }

    public function render()
    {
        return view('livewire.itinerary.route-weather');
    }
}

