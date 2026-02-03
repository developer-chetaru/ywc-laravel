<?php

namespace App\Livewire\Forum\Pages\Thread;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
use App\Services\Forum\ForumRoleAccessService;
use App\Services\Forum\ForumReputationService;
use App\Services\Forum\ForumNotificationService;
use App\Services\Forum\HtmlSanitizerService;
use App\Services\Forum\MentionService;
use Spatie\Permission\Models\Role;

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
    public bool $is_question = false;
    
    // Source module tracking
    public ?string $source_module = null;
    public ?int $source_item_id = null;
    public ?string $source_item_type = null;
    public ?string $source_item_title = null;
    public ?string $source_item_url = null;
    
    // Role management
    public bool $showRoleSelector = false;
    public string $searchRole = '';
    public array $selectedRoles = [];
    public $roles = [];

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
            Log::error('ThreadCreate: Category not found', [
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

        // Get source module parameters from query string
        $this->source_module = $request->query('source_module');
        $this->source_item_id = $request->query('source_item_id') ? (int)$request->query('source_item_id') : null;
        $this->source_item_type = $request->query('source_item_type');
        $this->source_item_title = $request->query('source_item_title');
        $this->source_item_url = $request->query('source_item_url');
        
        // Pre-populate title if source module data is available
        if ($this->source_item_title) {
            $this->title = "Discussion: " . $this->source_item_title;
        }

        UserCreatingThread::dispatch($request->user(), $this->category);
        
        // Load available roles
        $this->loadRoles();
    }
    
    public function loadRoles()
    {
        $query = Role::where('guard_name', 'web');
        
        if (!empty($this->searchRole)) {
            $query->where('name', 'like', '%' . $this->searchRole . '%');
        }
        
        $this->roles = $query->orderBy('name')->get();
    }
    
    public function updatedSearchRole()
    {
        $this->loadRoles();
    }
    
    public function toggleRoleSelector()
    {
        $this->showRoleSelector = !$this->showRoleSelector;
    }
    
    public function removeRole($roleId)
    {
        $this->selectedRoles = array_filter($this->selectedRoles, fn($id) => $id != $roleId);
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

        // Sanitize HTML content
        $sanitizer = app(HtmlSanitizerService::class);
        $sanitizedContent = $sanitizer->sanitize($validated['content']);

        $action = new Action($this->category, $request->user(), $validated['title'], $sanitizedContent);
        $thread = $action->execute();

        // Update thread with additional fields
        $updateData = [];
        
        if ($this->is_question) {
            $updateData['is_question'] = true;
        }
        
        // Add source module information if available
        if ($this->source_module && $this->source_item_id) {
            $updateData['source_module'] = $this->source_module;
            $updateData['source_item_id'] = $this->source_item_id;
            $updateData['source_item_type'] = $this->source_item_type;
        }
        
        if (!empty($updateData)) {
            $thread->update($updateData);
        }

        // Award reputation for creating thread
        $reputationService = app(ForumReputationService::class);
        $reputationService->awardThreadCreated($request->user(), $thread->id);

        // Process mentions and send notifications
        $mentionService = app(MentionService::class);
        $mentionedUsers = $mentionService->processMentions($sanitizedContent);
        $notificationService = app(ForumNotificationService::class);
        $thread = $thread->fresh(['author', 'category']);
        
        foreach ($mentionedUsers as $mentionedUser) {
            if ($mentionedUser->id !== $request->user()->id) {
                $firstPost = $thread->posts()->first();
                if ($firstPost) {
                    $notificationService->notifyMention($mentionedUser, $thread, $firstPost, $request->user());
                }
            }
        }

        // Send notifications to category subscribers (optional - can be added later)
        // For now, we'll skip this to avoid spam, but it can be enabled if needed

        // Set role restrictions for thread if any roles selected
        if (!empty($this->selectedRoles)) {
            $roleAccessService = app(ForumRoleAccessService::class);
            $roleNames = Role::whereIn('id', $this->selectedRoles)
                ->pluck('name')
                ->toArray();
            $roleAccessService->setThreadRoles($thread->id, $roleNames);
        }

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

