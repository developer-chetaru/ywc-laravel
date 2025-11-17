<?php

namespace App\Providers;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteReview;
use App\Policies\ItineraryRoutePolicy;
use App\Policies\ItineraryRouteReviewPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Forum\Pages\Category\ForumsList;
use App\View\Composers\CategoryIndexComposer;

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
        
        // Register forum Livewire components
        Livewire::component('forum::pages.category.forums-list', ForumsList::class);
        Livewire::component('forum::pages.category.threads-list', \App\Livewire\Forum\Pages\Category\ThreadsList::class);
        Livewire::component('forum::pages.category.create-forum', \App\Livewire\Forum\Pages\Category\CreateForum::class);
        
        // Register view composer for category index (with forum namespace)
        // This ensures variables are always available, regardless of which component renders the view
        View::composer('forum::pages.category.index', CategoryIndexComposer::class);
        
        // Override the package's CategoryIndex component after all service providers have booted
        // This ensures our override happens after the package registers its component
        $this->app->booted(function () {
            Livewire::component('pages.category.index', \App\Livewire\Forum\Pages\Category\CategoryIndex::class);
        });
    }
}
