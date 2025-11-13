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
            // Ensure route has stops loaded
            $this->route->loadMissing('stops');
            
            if ($this->route->stops->isEmpty()) {
                $this->message = 'No stops found on this route. Add stops to fetch weather data.';
                $this->syncing = false;
                return;
            }

            $service = app(WeatherService::class);
            
            // Count stops with coordinates before sync
            $stopsWithCoords = $this->route->stops->filter(function($stop) {
                return $stop->latitude && $stop->longitude && 
                       (float)$stop->latitude != 0 && (float)$stop->longitude != 0;
            })->count();
            
            if ($stopsWithCoords === 0) {
                $this->message = 'No stops have valid coordinates. Please add coordinates to stops to fetch weather data.';
                $this->syncing = false;
                return;
            }
            
            $service->syncRouteWeather($this->route, 7);
            
            $this->message = 'Weather data refreshed successfully.';
            $this->route->refresh();
            $this->route->load(['stops.weatherSnapshots']);
        } catch (\Exception $e) {
            \Log::error('Weather sync failed in Livewire component', [
                'route_id' => $this->route->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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

