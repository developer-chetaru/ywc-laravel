<?php

namespace App\Livewire\Forum\Pages\Post;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\EditPost,
    Actions\DeletePost,
    Events\UserEditingPost,
    Events\UserEditedPost,
    Events\UserDeletedPost,
    Models\Post,
    Support\Authorization\PostAuthorization,
    Support\Validation\PostRules,
};

class PostEdit extends Component
{
    #[Locked]
    public Post $post;

    // Form fields
    public string $content;

    public function mount(Request $request, $post_id = null)
    {
        $post = null;
        
        // PRIORITY 1: Livewire passes route parameters as method arguments
        if ($post_id) {
            // Convert to integer if it's a string number
            $id = is_numeric($post_id) ? (int)$post_id : $post_id;
            
            // Check if user can view trashed posts
            $query = Post::with('thread', 'author');
            if (\Illuminate\Support\Facades\Gate::allows('viewTrashedPosts')) {
                $query->withTrashed();
            }
            $post = $query->find($id);
        }
        
        // PRIORITY 2: Try to get post from route parameter (set by ResolveFrontendParameters middleware)
        if (!$post && $request->route('post')) {
            $post = $request->route('post');
        }
        
        // PRIORITY 3: Try to get from route parameters directly
        // The route uses format: /p/{post_id}
        if (!$post && $request->route()) {
            $routeParams = $request->route()->parameters();
            
            // Check for post_id parameter
            if (isset($routeParams['post_id'])) {
                $paramValue = $routeParams['post_id'];
                $query = Post::with('thread', 'author');
                if (\Illuminate\Support\Facades\Gate::allows('viewTrashedPosts')) {
                    $query->withTrashed();
                }
                
                // Handle if it's a numeric string or integer
                if (is_numeric($paramValue)) {
                    $post = $query->find((int)$paramValue);
                } elseif (is_string($paramValue) && strpos($paramValue, '-') !== false) {
                    // If in combined format (e.g., "1-post-slug"), extract it
                    $parts = explode('-', $paramValue, 2);
                    if (is_numeric($parts[0])) {
                        $post = $query->find((int)$parts[0]);
                    }
                }
            }
        }
        
        // PRIORITY 4: Try to get from query parameter
        if (!$post && $request->has('post_id')) {
            $query = Post::with('thread', 'author');
            if (\Illuminate\Support\Facades\Gate::allows('viewTrashedPosts')) {
                $query->withTrashed();
            }
            $post = $query->find($request->query('post_id'));
        }

        // If post is still null, abort with error
        if (!$post || !($post instanceof Post)) {
            \Log::error('PostEdit: Post not found', [
                'post_id_param' => $post_id,
                'route_post' => $request->route('post') ? 'exists' : 'null',
                'route_params' => $request->route() ? $request->route()->parameters() : 'no route',
                'query_params' => $request->query(),
                'url' => $request->fullUrl(),
            ]);
            abort(404, 'Post not found.');
        }

        $this->post = $post;
        $this->content = $this->post->content;

        if (!PostAuthorization::edit($request->user(), $this->post)) {
            abort(403);
        }

        UserEditingPost::dispatch($request->user(), $this->post);
    }

    public function save(Request $request)
    {
        if (!PostAuthorization::edit($request->user(), $this->post)) {
            abort(403);
        }

        $validated = $this->validate(PostRules::create());

        $action = new EditPost($this->post, $validated['content']);
        $post = $action->execute();

        UserEditedPost::dispatch($request->user(), $post);

        return $this->redirect($post->route);
    }

    public function delete(Request $request)
    {
        if (!PostAuthorization::delete($request->user(), $this->post)) {
            abort(403);
        }

        $thread = $this->post->thread;

        $action = new DeletePost($this->post);
        $action->execute();

        UserDeletedPost::dispatch($request->user(), $this->post);

        return $this->redirect($thread->route);
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.post.edit')
            ->layout('forum::layouts.main');
    }
}

