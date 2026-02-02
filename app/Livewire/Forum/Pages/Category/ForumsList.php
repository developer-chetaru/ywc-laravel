<?php

namespace App\Livewire\Forum\Pages\Category;

use Livewire\Component;
use App\Services\Forum\ForumRoleAccessService;
use TeamTeaTime\Forum\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class ForumsList extends Component
{
    public string $search = '';
    public string $status = '';

    protected ForumRoleAccessService $roleAccessService;

    public function boot(ForumRoleAccessService $roleAccessService)
    {
        $this->roleAccessService = $roleAccessService;
    }

    public function toggleStatus($id)
    {
        $forum = Category::findOrFail($id);
        
        // Check if status column exists, if not use accepts_threads as fallback
        if (Schema::hasColumn('forum_categories', 'status')) {
            $forum->status = !$forum->status;
            $forum->save();
        } else {
            // Fallback to accepts_threads if status doesn't exist
            $forum->accepts_threads = !$forum->accepts_threads;
            $forum->save();
        }
    }

    public function render()
    {
        $query = Category::query();

        // Apply role-based filtering (hide restricted categories from unauthorized users)
        if (Auth::check()) {
            $this->roleAccessService->filterCategoriesByAccess($query, Auth::user());
        }

        // Apply search filter
        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        // Apply status filter
        if ($this->status !== '') {
            if (Schema::hasColumn('forum_categories', 'status')) {
                $query->where('status', $this->status);
            } else {
                // Fallback to accepts_threads if status doesn't exist
                $query->where('accepts_threads', $this->status);
            }
        }

        $forums = $query->orderBy('created_at', 'desc')->get();

        // Calculate active forums count
        if (Schema::hasColumn('forum_categories', 'status')) {
            $activeForumsCount = Category::where('status', true)->count();
        } else {
            // Fallback to accepts_threads
            $activeForumsCount = Category::where('accepts_threads', true)->count();
        }

        return view('pages.category.forums-list', [
            'forums' => $forums,
            'activeForumsCount' => $activeForumsCount,
        ]);
    }
}
