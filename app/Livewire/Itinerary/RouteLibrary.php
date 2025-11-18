<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\MasterData;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RouteLibrary extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $region = null;

    #[Url]
    public ?string $difficulty = null;

    #[Url]
    public ?string $season = null;

    #[Url]
    public ?string $status = null;

    #[Url]
    public ?string $visibility = null;

    #[Url]
    public ?int $days = null;

    #[Url]
    public bool $templates = false;

    public array $regions = [];
    public array $difficulties = [];
    public array $seasons = [];
    public array $availableDays = [];

    public function mount(): void
    {
        $this->regions = ItineraryRoute::query()
            ->select('region')
            ->whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region')
            ->all();

        $this->difficulties = ItineraryRoute::query()
            ->select('difficulty')
            ->whereNotNull('difficulty')
            ->distinct()
            ->orderBy('difficulty')
            ->pluck('difficulty')
            ->all();

        $this->seasons = ItineraryRoute::query()
            ->select('season')
            ->whereNotNull('season')
            ->distinct()
            ->orderBy('season')
            ->pluck('season')
            ->all();

        // Get all unique duration_days values from database
        // Consider user's visibility permissions
        $user = Auth::user();
        $query = ItineraryRoute::query()
            ->select('duration_days')
            ->whereNotNull('duration_days')
            ->where('duration_days', '>', 0);
        
        // Apply same visibility rules as the main query
        if ($user) {
            $query->where(function ($sub) use ($user) {
                $sub->where('user_id', $user->id)
                    ->orWhere('visibility', 'public')
                    ->orWhere(function ($crewQuery) use ($user) {
                        $crewQuery->where('visibility', 'crew')
                            ->whereHas('crew', function ($crew) use ($user) {
                                $crew->where('user_id', $user->id)
                                    ->where('status', 'accepted');
                            });
                    });
            });
        } else {
            $query->where('visibility', 'public');
        }
        
        $this->availableDays = $query->distinct()
            ->orderBy('duration_days')
            ->pluck('duration_days')
            ->all();
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['search', 'region', 'difficulty', 'season', 'status', 'visibility', 'days', 'templates'], true)) {
            $this->resetPage();
        }
    }

    public function updatedSearch($value): void
    {
        // Ensure search triggers page reset and re-render
        $this->resetPage();
    }
    
    public function applyFilters(): void
    {
        // Method to manually apply filters (if needed)
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->region = null;
        $this->difficulty = null;
        $this->season = null;
        $this->status = null;
        $this->visibility = null;
        $this->days = null;
        $this->templates = false;
        $this->resetPage();
    }

    public function cloneRoute(int $routeId): void
    {
        $route = ItineraryRoute::findOrFail($routeId);
        $user = Auth::user();
        abort_unless($user, 403);

        if (!$route->visibleTo($user)) {
            abort(403);
        }

        $clone = app(\App\Services\Itinerary\RouteBuilder::class)->cloneRoute($route, $user);
        session()->flash('status', 'Route copied to your account.');
        $this->redirectRoute('itinerary.routes.show', $clone);
    }

    public function render()
    {
        $user = Auth::user();

        $query = ItineraryRoute::query()
            ->with(['owner:id,first_name,last_name', 'statistics'])
            ->when(!$user, fn ($q) => $q->public())
            ->when($this->search && trim($this->search) !== '', function ($q) {
                $search = trim($this->search);
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                });
            })
            ->when(!empty($this->region), fn ($q) => $q->where('region', $this->region))
            ->when(!empty($this->difficulty), fn ($q) => $q->where('difficulty', $this->difficulty))
            ->when(!empty($this->season), fn ($q) => $q->where('season', $this->season))
            ->when(!empty($this->status), fn ($q) => $q->where('status', $this->status))
            ->when(!empty($this->visibility), fn ($q) => $q->where('visibility', $this->visibility))
            ->when(!empty($this->days), fn ($q) => $q->where('duration_days', $this->days))
            ->when($this->templates === true, fn ($q) => $q->templates())
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->orderByDesc('published_at')
            ->orderBy('title');

        if ($user) {
            $query->where(function ($sub) use ($user) {
                $sub->where('user_id', $user->id) // User's own routes (any status/visibility)
                    ->orWhere('visibility', 'public') // Public routes
                    ->orWhere(function ($crewQuery) use ($user) {
                        $crewQuery->where('visibility', 'crew')
                            ->whereHas('crew', function ($crew) use ($user) {
                                $crew->where('user_id', $user->id)
                                    ->where('status', 'accepted');
                            });
                    });
            });
        }

        $routes = $query->paginate(9);

        $routeVisibility = MasterData::getRouteVisibility();
        $routeStatus = MasterData::getRouteStatus();

        return view('livewire.itinerary.route-library', [
            'routes' => $routes,
            'availableDays' => $this->availableDays,
            'routeVisibility' => $routeVisibility,
            'routeStatus' => $routeStatus,
        ]);
    }
}

