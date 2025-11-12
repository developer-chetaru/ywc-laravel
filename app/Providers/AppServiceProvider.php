<?php

namespace App\Providers;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteReview;
use App\Policies\ItineraryRoutePolicy;
use App\Policies\ItineraryRouteReviewPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ItineraryRoute::class, ItineraryRoutePolicy::class);
        Gate::policy(ItineraryRouteReview::class, ItineraryRouteReviewPolicy::class);
    }
}
