<?php

namespace App\Livewire\Forum\Pages\Category;

use Livewire\Component;
use Illuminate\Http\Request;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Actions\CreateCategory as CreateCategoryAction;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;
use TeamTeaTime\Forum\Support\Authorization\CategoryAuthorization;
use TeamTeaTime\Forum\Support\Validation\CategoryRules;
use TeamTeaTime\Forum\Events\UserCreatedCategory;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Services\Forum\ForumRoleAccessService;
use App\Services\Forum\ForumReputationService;

class CreateForum extends Component
{
    // Form fields
    public string $title = '';
    public string $description = '';
    public $parent_category = '';
    public string $threadTitle = '';
    public string $threadDescription = '';
    
    // Role management
    public bool $showUsersList = false;
    public string $searchRole = '';
    public array $selectedRoles = [];

    // Available data
    public array $availableCategories = [];
    public $roles = [];

    public function mount(Request $request)
    {
        if (!CategoryAuthorization::create($request->user())) {
            abort(403);
        }

        // Get available categories for parent selection
        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();
        $categories = CategoryAccess::removeParentRelationships($categories);
        
        $this->availableCategories = $this->formatCategoriesForSelect($categories);
        
        // Get all roles
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $query = Role::where('guard_name', 'web'); // Use web guard
        
        if (!empty($this->searchRole)) {
            $query->where('name', 'like', '%' . $this->searchRole . '%');
        }
        
        $this->roles = $query->orderBy('name')->get();
    }

    public function updatedSearchRole()
    {
        $this->loadRoles();
    }

    public function toggleUsersList()
    {
        $this->showUsersList = !$this->showUsersList;
    }

    public function removeRole($roleId)
    {
        $this->selectedRoles = array_filter($this->selectedRoles, fn($id) => $id != $roleId);
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->parent_category = '';
        $this->threadTitle = '';
        $this->threadDescription = '';
        $this->selectedRoles = [];
        $this->showUsersList = false;
        $this->searchRole = '';
        $this->loadRoles();
    }

    public function store(Request $request)
    {
        if (!CategoryAuthorization::create($request->user())) {
            abort(403);
        }

        // Normalize parent_category first (convert empty string to null)
        if (empty($this->parent_category) || $this->parent_category === '' || $this->parent_category === '0') {
            $this->parent_category = null;
        }
        
        // Validate category data
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'threadTitle' => 'nullable|string|max:255',
            'threadDescription' => 'nullable|string',
        ];
        
        // Only validate parent_category if it's not null
        if ($this->parent_category !== null) {
            $rules['parent_category'] = 'required|integer|exists:forum_categories,id';
        }
        
        $validated = $this->validate($rules);
        
        // Ensure parent_category is set correctly
        $validated['parent_category'] = $this->parent_category;

        try {
            DB::beginTransaction();

            // Create category using the package's action
            $defaultColor = config('forum.frontend.default_category_color', '#007bff');
            $action = new CreateCategoryAction(
                $validated['title'],
                $validated['description'] ?? '',
                $defaultColor,
                $defaultColor,
                true, // accepts_threads
                false // is_private
            );
            
            $category = $action->execute();

            // Set parent category if provided
            if (!empty($validated['parent_category']) && $validated['parent_category'] > 0) {
                $parent = Category::find($validated['parent_category']);
                if ($parent) {
                    $parent->appendNode($category);
                }
            }

            // Set role restrictions for category if any roles selected
            if (!empty($this->selectedRoles)) {
                $roleAccessService = app(ForumRoleAccessService::class);
                $roleNames = Role::whereIn('id', $this->selectedRoles)
                    ->pluck('name')
                    ->toArray();
                $roleAccessService->setCategoryRoles($category->id, $roleNames);
            }

            // Create initial thread if provided
            if (!empty($validated['threadTitle'])) {
                $thread = Thread::create([
                    'category_id' => $category->id,
                    'author_id' => $request->user()->id,
                    'title' => $validated['threadTitle'],
                    'pinned' => false,
                    'locked' => false,
                ]);

                // Award reputation for creating thread (badge checking happens automatically inside)
                $reputationService = app(ForumReputationService::class);
                $reputationService->awardThreadCreated($request->user(), $thread->id);

                // Create first post (thread content) - threads require at least one post
                $postContent = !empty($validated['threadDescription']) 
                    ? $validated['threadDescription'] 
                    : $validated['threadTitle']; // Use title as content if description is empty
                    
                $post = Post::create([
                    'thread_id' => $thread->id,
                    'author_id' => $request->user()->id,
                    'content' => $postContent,
                ]);
                
                // Award reputation for first post (reply)
                $reputationService->awardReplyPosted($request->user(), $post->id);
            }

            // Dispatch event
            UserCreatedCategory::dispatch($request->user(), $category);

            DB::commit();

            session()->flash('success', 'Forum created successfully!');
            
            // Reset form
            $this->resetForm();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating forum: ' . $e->getMessage());
        }
    }

    protected function formatCategoriesForSelect($categories, $prefix = '')
    {
        $formatted = [];
        
        foreach ($categories as $category) {
            $formatted[$category->id] = $prefix . $category->title;
            
            // Handle children recursively if any
            if (isset($category->children) && is_array($category->children) && count($category->children) > 0) {
                $formatted = array_merge($formatted, $this->formatCategoriesForSelect($category->children, $prefix . '  └─ '));
            }
        }
        
        return $formatted;
    }

    public function render()
    {
        return view('pages.category.create-forum');
    }
}
