<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TeamTeaTime\Forum\Http\Livewire\Pages\ThreadView;
use Illuminate\Support\Facades\Route;
use TeamTeaTime\Forum\Http\Livewire\Pages\CreateForum;
use TeamTeaTime\Forum\Http\Livewire\Pages\ForumsList;
use TeamTeaTime\Forum\Http\Livewire\Pages\ThreadsList;

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
        Livewire::component('forum::categories.thread-view', ThreadView::class);
        Livewire::component('forum::pages.category.create-forum', CreateForum::class);
        Livewire::component('forum::pages.category.forums-list', ForumsList::class);
        Livewire::component('forum::pages.category.threads-list', ThreadsList::class);
        // Override forum routes with your middleware
        Route::middleware([
            'web',
            'auth:sanctum',
            config('jetstream.auth_session'),
            'verified',
            'subscribed',
            'setlocale'
        ])->group(function () {
            $prefix = config('forum.frontend.route_prefixes');

            Route::get('/', \TeamTeaTime\Forum\Http\Livewire\Pages\CategoryIndex::class)
                ->name('category.index');

            Route::get('category/order', \TeamTeaTime\Forum\Http\Livewire\Pages\UpdateCategoryTree::class)
                ->name('category.order');

            Route::get('category/create', \TeamTeaTime\Forum\Http\Livewire\Pages\CategoryCreate::class)
                ->name('category.create');

            Route::get('categories/forum', \TeamTeaTime\Forum\Http\Livewire\Pages\CreateForum::class)
                ->name('category.forum');

            Route::get('categories/forums-list', \TeamTeaTime\Forum\Http\Livewire\Pages\ForumsList::class)
                ->name('category.forums-list');

            Route::get('categories/threads', \TeamTeaTime\Forum\Http\Livewire\Pages\ThreadsList::class)
                ->name('category.threads-list');

            Route::get('recent', \TeamTeaTime\Forum\Http\Livewire\Pages\RecentThreads::class)
                ->name('recent');

            Route::get('unread', \TeamTeaTime\Forum\Http\Livewire\Pages\UnreadThreads::class)
                ->name('unread');

            Route::get('categories/threads/{thread?}', \TeamTeaTime\Forum\Http\Livewire\Pages\CategoryThreadView::class)
                ->name('category.threadview');

            Route::group(['prefix' => $prefix['category'] . '/{category_id}-{category_slug}'], function () use ($prefix) {
                Route::get('/', \TeamTeaTime\Forum\Http\Livewire\Pages\CategoryShow::class)
                    ->name('category.show');
                Route::get('edit', \TeamTeaTime\Forum\Http\Livewire\Pages\CategoryEdit::class)
                    ->name('category.edit');
                Route::get($prefix['thread'] . '/create', \TeamTeaTime\Forum\Http\Livewire\Pages\ThreadCreate::class)
                    ->name('thread.create');
            });

            Route::group(['prefix' => $prefix['thread'] . '/{thread_id}-{thread_slug}'], function () use ($prefix) {
                Route::get('/', \TeamTeaTime\Forum\Http\Livewire\Pages\ThreadShow::class)
                    ->name('thread.show');
                Route::get('reply', \TeamTeaTime\Forum\Http\Livewire\Pages\ThreadReply::class)
                    ->name('thread.reply');
                Route::get($prefix['post'] . '/{post_id}/edit', \TeamTeaTime\Forum\Http\Livewire\Pages\PostEdit::class)
                    ->name('post.edit');
                Route::get($prefix['post'] . '/{post_id}', \TeamTeaTime\Forum\Http\Livewire\Pages\PostShow::class)
                    ->name('post.show');
            });
        });
    }
}
