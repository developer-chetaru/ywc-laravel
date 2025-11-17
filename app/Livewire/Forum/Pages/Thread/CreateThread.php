<?php

namespace App\Livewire\Forum\Pages\Thread;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\CreateThread as Action,
    Events\UserCreatingThread,
    Events\UserCreatedThread,
    Models\Category,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\ThreadRules,
};

class CreateThread extends Component
{
    // View data
    #[Locked]
    public $category = null;
    #[Locked]
    public $breadcrumbs_append = null;

    // Form fields
    public string $title = '';
    public string $content = '';

    public function mount(Request $request, $category_id = null, $category_slug = null)
    {
        $category = null;
        
        // PRIORITY 1: Livewire passes route parameters as method arguments
        // This is the most reliable since we know it's being passed
        if ($category_id) {
            // Convert to integer if it's a string number
            $id = is_numeric($category_id) ? (int)$category_id : $category_id;
            $category = Category::find($id);
        }
        
        // PRIORITY 2: Try to get category from route parameter (set by ResolveFrontendParameters middleware)
        if (!$category && $request->route('category')) {
            $category = $request->route('category');
        }
        
        // PRIORITY 3: Try to get from route parameters directly
        // The route uses format: /c/{category_id}-{category_slug}
        if (!$category && $request->route()) {
            $routeParams = $request->route()->parameters();
            
            // Check for category_id parameter
            if (isset($routeParams['category_id'])) {
                $paramValue = $routeParams['category_id'];
                // Handle if it's a numeric string or integer
                if (is_numeric($paramValue)) {
                    $category = Category::find((int)$paramValue);
                } elseif (is_string($paramValue) && strpos($paramValue, '-') !== false) {
                    // If in combined format (e.g., "1-category-name"), extract it
                    $parts = explode('-', $paramValue, 2);
                    if (is_numeric($parts[0])) {
                        $category = Category::find((int)$parts[0]);
                    }
                }
            }
        }
        
        // PRIORITY 4: Try to get from query parameter
        if (!$category && $request->has('category_id')) {
            $category = Category::find($request->query('category_id'));
        }

        // If category is still null, abort with error
        if (!$category || !($category instanceof Category)) {
            \Log::error('ThreadCreate: Category not found', [
                'category_id_param' => $category_id,
                'category_slug_param' => $category_slug,
                'route_category' => $request->route('category') ? 'exists' : 'null',
                'route_params' => $request->route() ? $request->route()->parameters() : 'no route',
                'query_params' => $request->query(),
                'url' => $request->fullUrl(),
            ]);
            abort(404, 'Category not found. Please select a valid category to create a thread.');
        }
        
        $this->category = $category;

        $this->breadcrumbs_append = [trans('forum::threads.new_thread')];

        if (!CategoryAuthorization::createThreads($request->user(), $this->category)) {
            abort(403, 'You do not have permission to create threads in this category.');
        }

        UserCreatingThread::dispatch($request->user(), $this->category);
    }

    public function create(Request $request)
    {
        if (!$this->category) {
            session()->flash('error', 'Category not found.');
            return;
        }

        if (!CategoryAuthorization::createThreads($request->user(), $this->category)) {
            abort(403, 'You do not have permission to create threads in this category.');
        }

        $validated = $this->validate(ThreadRules::create());

        $action = new Action($this->category, $request->user(), $validated['title'], $validated['content']);
        $thread = $action->execute();

        UserCreatedThread::dispatch($request->user(), $thread);

        session()->flash('success', 'Thread created successfully!');

        return $this->redirect($thread->route);
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.thread.create')
            ->layout('forum::layouts.main');
    }
}

