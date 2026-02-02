<?php

namespace App\Policies;

use App\Models\User;
use App\Services\Forum\ForumRoleAccessService;
use TeamTeaTime\Forum\Models\Category;

class CategoryPolicy
{
    protected ForumRoleAccessService $roleAccessService;

    public function __construct(ForumRoleAccessService $roleAccessService)
    {
        $this->roleAccessService = $roleAccessService;
    }

    public function viewAny(User $user): bool
    {
        // All authenticated users can view categories list
        // But individual categories will be filtered by role access
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        // Check role-based access for this category
        $hasAccess = $this->roleAccessService->canAccessCategory($user, $category->id);
        
        if (!$hasAccess) {
            // Redirect to access denied page
            abort(403, 'Access denied');
        }
        
        return true;
    }

    public function create(User $user): bool
    {
        // Only authenticated users can create categories
        // (Admins/moderators can be restricted later)
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        // Only admins/moderators can update categories
        // For now, allow if user has admin role
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Category $category): bool
    {
        // Only admins can delete categories
        return $user->hasRole('super_admin');
    }

    public function createThreads(User $user, Category $category): bool
    {
        // Check if category accepts threads
        if (!$category->accepts_threads) {
            return false;
        }

        // Check role-based access for this category
        $hasAccess = $this->roleAccessService->canAccessCategory($user, $category->id);
        
        if (!$hasAccess) {
            return false;
        }

        // All authenticated users with category access can create threads
        return true;
    }

    public function createThreadsInCategory(User $user, Category $category): bool
    {
        // Alias for createThreads to match policy naming
        return $this->createThreads($user, $category);
    }
}
