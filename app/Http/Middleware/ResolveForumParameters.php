<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;

class ResolveForumParameters
{
    /**
     * Resolve forum frontend route parameters.
     * Handles combined format like {category_id}-{category_slug}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $parameters = $request->route()->parameters();

        if (array_key_exists('category_id', $parameters)) {
            // Extract numeric ID from combined format "2-test" -> 2
            $categoryId = $this->extractId($parameters['category_id']);
            
            if ($categoryId === null) {
                throw new NotFoundHttpException("Failed to resolve 'category' route parameter: Invalid category ID format.");
            }

            $category = Category::find($categoryId);

            if ($category === null) {
                throw new NotFoundHttpException("Failed to resolve 'category' route parameter: Category not found.");
            }

            $request->route()->setParameter('category', $category);
        }

        if (array_key_exists('thread_id', $parameters)) {
            // Extract numeric ID from combined format "123-thread-slug" -> 123
            $threadId = $this->extractId($parameters['thread_id']);
            
            if ($threadId === null) {
                throw new NotFoundHttpException("Failed to resolve 'thread' route parameter: Invalid thread ID format.");
            }

            $query = Thread::with('category');

            if (Gate::allows('viewTrashedThreads')) {
                $query->withTrashed();
            }

            $thread = $query->find($threadId);

            if ($thread === null) {
                throw new NotFoundHttpException("Failed to resolve 'thread' route parameter: Thread not found.");
            }

            $request->route()->setParameter('thread', $thread);
        }

        if (array_key_exists('post_id', $parameters)) {
            // Post ID is usually just numeric, but handle combined format just in case
            $postId = $this->extractId($parameters['post_id']);
            
            if ($postId === null) {
                throw new NotFoundHttpException("Failed to resolve 'post' route parameter: Invalid post ID format.");
            }

            $query = Post::with(['thread', 'thread.category']);

            if (Gate::allows('viewTrashedPosts')) {
                $query->withTrashed();
            }

            $post = $query->find($postId);

            if ($post === null) {
                throw new NotFoundHttpException("Failed to resolve 'post' route parameter: Post not found.");
            }

            $request->route()->setParameter('post', $post);
        }

        return $next($request);
    }

    /**
     * Extract numeric ID from combined format like "2-test" or "123-thread-slug"
     * Returns null if no valid ID found
     */
    private function extractId($value): ?int
    {
        // If it's already numeric, return it
        if (is_numeric($value)) {
            return (int) $value;
        }

        // If it's a string, extract the numeric part before the first dash
        if (is_string($value)) {
            $parts = explode('-', $value, 2);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                return (int) $parts[0];
            }
        }

        return null;
    }
}
