<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class RouteAnalytics extends Component
{
    public ItineraryRoute $route;
    public string $period = '30'; // days

    public function mount(ItineraryRoute $route): void
    {
        $this->route = $route->load([
            'statistics',
            'reviews',
            'crew',
        ]);
    }

    public function updatedPeriod(): void
    {
        $this->route->load(['statistics', 'reviews']);
    }

    public function render()
    {
        Gate::authorize('view', $this->route);

        // Get statistics (one-to-one relationship)
        $statistics = $this->route->statistics;
        
        // Use route's own counts and statistics
        $totalViews = $statistics?->views_total ?? $this->route->views_count ?? 0;
        $totalCopies = $statistics?->copies_total ?? $this->route->copies_count ?? 0;
        $totalReviews = $this->route->reviews()->count();
        $averageRating = $this->route->reviews()->avg('rating') ?? $this->route->rating_avg ?? 0;

        // Region distribution from analytics JSON or use route's region
        $regionStats = [];
        if ($statistics?->regions_breakdown) {
            $regionStats = $statistics->regions_breakdown;
        } elseif ($this->route->region) {
            $regionStats = [$this->route->region => $totalViews];
        }

        // Daily views from analytics JSON or create placeholder
        $dailyViews = [];
        if ($statistics?->analytics && isset($statistics->analytics['daily_views'])) {
            $dailyViews = $statistics->analytics['daily_views'];
        } else {
            // Create a simple placeholder based on period
            $days = (int) $this->period;
            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dailyViews[$date] = 0;
            }
        }

        // Reviews over time
        $reviewsByMonth = $this->route->reviews()
            ->where('created_at', '>=', now()->subDays((int) $this->period))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return view('livewire.itinerary.route-analytics', [
            'totalViews' => $totalViews,
            'totalCopies' => $totalCopies,
            'totalReviews' => $totalReviews,
            'averageRating' => round($averageRating, 1),
            'regionStats' => $regionStats,
            'dailyViews' => $dailyViews,
            'reviewsByMonth' => $reviewsByMonth,
        ]);
    }
}

