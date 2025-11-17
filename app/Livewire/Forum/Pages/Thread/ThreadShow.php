<?php

namespace App\Livewire\Forum\Pages\Thread;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\Bulk\DeletePosts,
    Actions\Bulk\RestorePosts,
    Events\UserBulkDeletedPosts,
    Events\UserBulkRestoredPosts,
    Events\UserViewingThread,
    Http\Livewire\Forms\ThreadEditForm,
    Http\Livewire\Forms\ThreadReplyForm,
    Http\Livewire\Traits\CreatesAlerts,
    Http\Livewire\Traits\UpdatesContent,
    Http\Livewire\EventfulPaginatedComponent,
    Models\Category,
    Models\Thread,
    Support\Access\CategoryAccess,
    Support\Authorization\PostAuthorization,
    Support\Traits\HandlesDeletion,
};

class ThreadShow extends EventfulPaginatedComponent
{
    use CreatesAlerts, UpdatesContent, HandlesDeletion;

    public Thread $thread;

    public ThreadEditForm $threadEditForm;
    public int $destinationCategoryId = 0;

    public ThreadReplyForm $threadReplyForm;

    // Listen for new posts added via QuickReply
    protected $listeners = ['postAdded' => 'refreshPosts'];

    public function refreshPosts($postId = null)
    {
        // Reload the thread to get fresh posts
        $this->thread->refresh();
        
        // Touch the update key to force re-render
        $this->touchUpdateKey();
    }

    public function mount(Request $request, $thread_id = null, $thread_slug = null)
    {
        $thread = null;
        
        // PRIORITY 1: Livewire passes route parameters as method arguments
        // This is the most reliable since we know it's being passed
        if ($thread_id) {
            // Convert to integer if it's a string number
            $id = is_numeric($thread_id) ? (int)$thread_id : $thread_id;
            
            // Check if user can view trashed threads
            $query = Thread::with('category');
            if (\Illuminate\Support\Facades\Gate::allows('viewTrashedThreads')) {
                $query->withTrashed();
            }
            $thread = $query->find($id);
        }
        
        // PRIORITY 2: Try to get thread from route parameter (set by ResolveFrontendParameters middleware)
        if (!$thread && $request->route('thread')) {
            $thread = $request->route('thread');
        }
        
        // PRIORITY 3: Try to get from route parameters directly
        // The route uses format: /t/{thread_id}-{thread_slug}
        if (!$thread && $request->route()) {
            $routeParams = $request->route()->parameters();
            
            // Check for thread_id parameter
            if (isset($routeParams['thread_id'])) {
                $paramValue = $routeParams['thread_id'];
                $query = Thread::with('category');
                if (\Illuminate\Support\Facades\Gate::allows('viewTrashedThreads')) {
                    $query->withTrashed();
                }
                
                // Handle if it's a numeric string or integer
                if (is_numeric($paramValue)) {
                    $thread = $query->find((int)$paramValue);
                } elseif (is_string($paramValue) && strpos($paramValue, '-') !== false) {
                    // If in combined format (e.g., "1-thread-name"), extract it
                    $parts = explode('-', $paramValue, 2);
                    if (is_numeric($parts[0])) {
                        $thread = $query->find((int)$parts[0]);
                    }
                }
            }
        }
        
        // PRIORITY 4: Try to get from query parameter
        if (!$thread && $request->has('thread_id')) {
            $query = Thread::with('category');
            if (\Illuminate\Support\Facades\Gate::allows('viewTrashedThreads')) {
                $query->withTrashed();
            }
            $thread = $query->find($request->query('thread_id'));
        }

        // If thread is still null, abort with error
        if (!$thread || !($thread instanceof Thread)) {
            \Log::error('ThreadShow: Thread not found', [
                'thread_id_param' => $thread_id,
                'thread_slug_param' => $thread_slug,
                'route_thread' => $request->route('thread') ? 'exists' : 'null',
                'route_params' => $request->route() ? $request->route()->parameters() : 'no route',
                'query_params' => $request->query(),
                'url' => $request->fullUrl(),
            ]);
            abort(404, 'Thread not found.');
        }

        $this->thread = $thread;
        $this->threadEditForm->title = $this->thread->title;
        $this->title = $this->thread->title;

        if (!$this->thread->category->isAccessibleTo($request->user())) {
            abort(404);
        }

        if ($request->user() !== null) {
            UserViewingThread::dispatch($request->user(), $this->thread);
            $this->thread->markAsRead($request->user());
        }

        $this->touchUpdateKey();
    }

    public function delete(Request $request, bool $permadelete)
    {
        $this->thread = $this->threadEditForm->delete($request, $this->thread, $permadelete);

        return $this->redirect($this->thread->category->route);
    }

    public function restore(Request $request): array
    {
        $this->thread = $this->threadEditForm->restore($request, $this->thread);

        return $this->pluralAlert('threads.restored')->toLivewire();
    }

    public function lock(Request $request): array
    {
        $this->thread = $this->threadEditForm->lock($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function unlock(Request $request): array
    {
        $this->thread = $this->threadEditForm->unlock($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function pin(Request $request): array
    {
        $this->thread = $this->threadEditForm->pin($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function unpin(Request $request): array
    {
        $this->thread = $this->threadEditForm->unpin($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function rename(Request $request): array
    {
        $this->thread = $this->threadEditForm->rename($request, $this->thread);

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function move(Request $request): array
    {
        $destination = Category::find($this->destinationCategoryId);

        if ($destination == null) {
            return $this->invalidSelectionAlert()->toLivewire();
        }

        $this->threadEditForm->move($request, $this->thread, $destination);
        $this->thread->category = $destination;
        $this->destinationCategoryId = 0;

        return $this->pluralAlert('threads.updated')->toLivewire();
    }

    public function reply(Request $request): array
    {
        $post = $this->threadReplyForm->reply($request, $this->thread);

        $this->setPage($post->getPage());
        $this->touchUpdateKey();

        return $this->alert('general.reply_added')->toLivewire();
    }

    public function deletePosts(Request $request, array $postIds, bool $permadelete): array
    {
        if (!PostAuthorization::bulkDelete($request->user(), $postIds)) {
            abort(403);
        }

        $action = new DeletePosts($postIds, $this->shouldPermaDelete($permadelete));
        $result = $action->execute();

        $this->touchUpdateKey();

        if ($result !== null) {
            UserBulkDeletedPosts::dispatch($request->user(), $result);
        }

        return $this->pluralAlert('posts.deleted', $result->count())->toLivewire();
    }

    public function restorePosts(Request $request, array $postIds): array
    {
        if (!PostAuthorization::bulkRestore($request->user(), $postIds)) {
            abort(403);
        }

        $action = new RestorePosts($postIds);
        $result = $action->execute();

        $this->touchUpdateKey();

        if ($result !== null) {
            UserBulkRestoredPosts::dispatch($request->user(), $result);
        }

        return $this->pluralAlert('posts.restored', $result->count())->toLivewire();
    }

    public function render(Request $request): View
    {
        $threadDestinationCategories = $request->user() && $request->user()->can('moveThreadsFrom', $this->thread->category)
            ? CategoryAccess::getFilteredTreeFor($request->user())->toTree()
            : [];

        $postsQuery = config('forum.general.display_trashed_posts') || $request->user() && $request->user()->can('viewTrashedPosts')
            ? $this->thread->posts()->withTrashed()
            : $this->thread->posts();

        // Exclude the first post (sequence 0) as it's shown separately in ThreadContent
        $posts = $postsQuery
            ->where('sequence', '>', 0)
            ->with('author', 'thread')
            ->orderBy('created_at', 'asc')
            ->paginate();

        $selectablePostIds = [];
        if ($request->user()) {
            foreach ($posts as $post) {
                if ($post->sequence > 1 && ($request->user()->can('delete', $post) || $request->user()->can('restore', $post))) {
                    $selectablePostIds[] = $post->id;
                }
            }
        }

        return ViewFactory::make('forum::pages.thread.show', [
            'posts' => $posts,
            'threadDestinationCategories' => $threadDestinationCategories,
            'selectablePostIds' => $selectablePostIds,
        ])->layout('forum::layouts.main', ['category' => $this->thread->category, 'thread' => $this->thread]);
    }
}

