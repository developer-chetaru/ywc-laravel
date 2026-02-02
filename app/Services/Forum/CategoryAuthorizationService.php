<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Category;
use App\Services\Forum\ForumRoleAccessService;

class CategoryAuthorizationService
{
    protected ForumRoleAccessService $roleAccessService;

    public function __construct(ForumRoleAccessService $roleAccessService)
    {
        $this->roleAccessService = $roleAccessService;
    }

    /**
     * Check if user can create threads in category
     */
    public static function createThreads(?User $user, Category $category): bool
    {
        // User must be authenticated
        if (!$user) {
            return false;
        }

        // Category must accept threads
        if (!$category->accepts_threads) {
            return false;
        }

        // Check role-based access for this category
        $roleAccessService = app(ForumRoleAccessService::class);
        $hasAccess = $roleAccessService->canAccessCategory($user, $category->id);
        
        if (!$hasAccess) {
            return false;
        }

        // All authenticated users with category access can create threads
        return true;
    }
}
