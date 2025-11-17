<?php

namespace App\Livewire\Forum\Pages\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\EditCategory,
    Events\UserDeletedCategory,
    Events\UserEditingCategory,
    Events\UserEditedCategory,
    Models\Category,
    Support\Access\CategoryAccess,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\CategoryRules,
    Support\Frontend\Forum,
};

class CategoryEdit extends Component
{
    #[Locked]
    public Category $category;

    #[Locked]
    public Collection $categories;

    // Form fields
    public string $title;
    public string $description;
    public string $color_light_mode;
    public string $color_dark_mode;
    public ?int $parent_category = null;
    public bool $accepts_threads = false;
    public bool $is_private = false;

    public function mount(Request $request, $category_id = null, $category_slug = null)
    {
        // Debug: Log that our custom component is being used
        \Log::info('Custom CategoryEdit component mount() called', [
            'category_id_param' => $category_id,
            'category_slug_param' => $category_slug,
            'route_exists' => $request->route() !== null,
        ]);
        
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
            \Log::error('CategoryEdit: Category not found', [
                'category_id_param' => $category_id,
                'category_slug_param' => $category_slug,
                'route_category' => $request->route('category') ? 'exists' : 'null',
                'route_params' => $request->route() ? $request->route()->parameters() : 'no route',
                'query_params' => $request->query(),
                'url' => $request->fullUrl(),
            ]);
            abort(404, 'Category not found. Please select a valid category to edit.');
        }

        if (!CategoryAuthorization::edit($request->user(), $category)) {
            abort(403, 'You do not have permission to edit this category.');
        }

        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Once the issue is fixed, this can be removed.
        $this->categories = CategoryAccess::removeParentRelationships($categories);

        $this->category = $category;
        $this->title = $category->title;
        $this->description = $category->description ?? "";
        $this->color_light_mode = $category->color_light_mode;
        $this->color_dark_mode = $category->color_dark_mode;
        $this->parent_category = $category->parent_id;
        $this->accepts_threads = $category->accepts_threads;
        $this->is_private = $category->is_private;

        UserEditingCategory::dispatch($request->user(), $category);
    }

    public function save(Request $request)
    {
        if (!$this->category) {
            session()->flash('error', 'Category not found.');
            return;
        }

        if (!CategoryAuthorization::edit($request->user(), $this->category)) {
            abort(403, 'You do not have permission to edit this category.');
        }

        $validated = $this->validate(CategoryRules::create());

        $action = new EditCategory($this->category, $validated['title'], $validated['description'], $validated['color_light_mode'], $validated['color_dark_mode'], $validated['accepts_threads'], $validated['is_private']);
        $action->execute();

        if ($validated['parent_category'] > 0) {
            $parent = Category::find($validated['parent_category']);
            if ($parent) {
                $parent->appendNode($this->category);
            }
        }

        UserEditedCategory::dispatch($request->user(), $this->category);

        session()->flash('success', 'Category updated successfully!');

        return $this->redirect($this->category->route);
    }

    public function delete(Request $request)
    {
        if (!$this->category) {
            session()->flash('error', 'Category not found.');
            return;
        }

        if (!CategoryAuthorization::delete($request->user(), $this->category)) {
            abort(403, 'You do not have permission to delete this category.');
        }

        $this->category->delete();

        UserDeletedCategory::dispatch($request->user(), $this->category);

        session()->flash('success', 'Category deleted successfully!');

        return $this->redirect(Forum::route('category.index'));
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.edit')
            ->layout('forum::layouts.main');
    }
}

