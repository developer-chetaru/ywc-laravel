<?php

namespace App\Providers;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteReview;
use App\Models\Yacht;
use App\Policies\ItineraryRoutePolicy;
use App\Policies\ItineraryRouteReviewPolicy;
use App\Policies\YachtPolicy;
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
        // Register forum component overrides EARLY in the register() method
        // This ensures they're available before routes are loaded
        $this->overrideForumComponents();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ItineraryRoute::class, ItineraryRoutePolicy::class);
        Gate::policy(ItineraryRouteReview::class, ItineraryRouteReviewPolicy::class);
        Gate::policy(Yacht::class, YachtPolicy::class);
        
        // Register forum Livewire components
        Livewire::component('forum::pages.category.forums-list', ForumsList::class);
        Livewire::component('forum::pages.category.threads-list', \App\Livewire\Forum\Pages\Category\ThreadsList::class);
        Livewire::component('forum::pages.category.create-forum', \App\Livewire\Forum\Pages\Category\CreateForum::class);
        Livewire::component('forum::categories.thread-view', \App\Livewire\Forum\Pages\Category\ThreadView::class);
        
        // Register view composer for category index (with forum namespace)
        // This ensures variables are always available, regardless of which component renders the view
        View::composer('forum::pages.category.index', CategoryIndexComposer::class);
        
        // Override the package's components IMMEDIATELY
        // We need to register before routes are loaded, so register in boot() directly
        $this->overrideForumComponents();
        
        // Also register after BladeCompiler is resolved (when package registers)
        $this->callAfterResolving(\Illuminate\View\Compilers\BladeCompiler::class, function () {
            $this->overrideForumComponents();
        });
        
        // Also register after all service providers have booted (safety net)
        $this->app->booted(function () {
            $this->overrideForumComponents();
        });
    }
    
    /**
     * Override forum package components with our custom implementations
     */
    protected function overrideForumComponents(): void
    {
        // Override CategoryIndex component - register with multiple name formats
        $categoryIndexClass = \App\Livewire\Forum\Pages\Category\CategoryIndex::class;
        Livewire::component('forum::pages.category.index', $categoryIndexClass);
        Livewire::component('pages.category.index', $categoryIndexClass);
        Livewire::component('app.livewire.forum.pages.category.category-index', $categoryIndexClass);
        Livewire::component('team-tea-time.forum.http.livewire.pages.category-index', $categoryIndexClass);
        
        // Override CategoryEdit component - register with multiple name formats
        $categoryEditClass = \App\Livewire\Forum\Pages\Category\CategoryEdit::class;
        Livewire::component('forum::pages.category.edit', $categoryEditClass);
        Livewire::component('pages.category.edit', $categoryEditClass);
        Livewire::component('app.livewire.forum.pages.category.category-edit', $categoryEditClass);
        Livewire::component('team-tea-time.forum.http.livewire.pages.category-edit', $categoryEditClass);
        
        // Override ThreadCreate component - register with multiple name formats
        $threadCreateClass = \App\Livewire\Forum\Pages\Thread\CreateThread::class;
        Livewire::component('forum::pages.thread.create', $threadCreateClass);
        Livewire::component('pages.thread.create', $threadCreateClass);
        Livewire::component('app.livewire.forum.pages.thread.create-thread', $threadCreateClass);
        Livewire::component('team-tea-time.forum.http.livewire.pages.thread-create', $threadCreateClass);
        
        // Override ThreadShow component - register with multiple name formats
        $threadShowClass = \App\Livewire\Forum\Pages\Thread\ThreadShow::class;
        Livewire::component('forum::pages.thread.show', $threadShowClass);
        Livewire::component('pages.thread.show', $threadShowClass);
        Livewire::component('app.livewire.forum.pages.thread.thread-show', $threadShowClass);
        Livewire::component('team-tea-time.forum.http.livewire.pages.thread-show', $threadShowClass);
        
        // Override PostEdit component - register with multiple name formats
        $postEditClass = \App\Livewire\Forum\Pages\Post\PostEdit::class;
        Livewire::component('forum::pages.post.edit', $postEditClass);
        Livewire::component('pages.post.edit', $postEditClass);
        Livewire::component('app.livewire.forum.pages.post.post-edit', $postEditClass);
        Livewire::component('team-tea-time.forum.http.livewire.pages.post-edit', $postEditClass);
        
        // CRITICAL: Override by class name to ensure Livewire resolves correctly
        // When routes use CategoryEdit::class directly, Livewire resolves by class name
        // This MUST be registered for the override to work
        Livewire::component(\TeamTeaTime\Forum\Http\Livewire\Pages\CategoryEdit::class, $categoryEditClass);
        Livewire::component(\TeamTeaTime\Forum\Http\Livewire\Pages\ThreadCreate::class, $threadCreateClass);
        Livewire::component(\TeamTeaTime\Forum\Http\Livewire\Pages\ThreadShow::class, $threadShowClass);
        Livewire::component(\TeamTeaTime\Forum\Http\Livewire\Pages\PostEdit::class, $postEditClass);
        Livewire::component(\TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex::class, $categoryIndexClass);
        
        // Also register using the full namespace path as string (alternative resolution method)
        Livewire::component('TeamTeaTime\Forum\Http\Livewire\Pages\CategoryEdit', $categoryEditClass);
        Livewire::component('TeamTeaTime\Forum\Http\Livewire\Pages\ThreadCreate', $threadCreateClass);
        Livewire::component('TeamTeaTime\Forum\Http\Livewire\Pages\ThreadShow', $threadShowClass);
        Livewire::component('TeamTeaTime\Forum\Http\Livewire\Pages\PostEdit', $postEditClass);
        Livewire::component('TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex', $categoryIndexClass);
    }
}
