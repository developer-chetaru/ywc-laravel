<?php

namespace App\Policies;

use App\Models\User;
use App\Services\Forum\ForumRoleAccessService;
use TeamTeaTime\Forum\Models\Thread;

class ThreadPolicy
{
    protected ForumRoleAccessService $roleAccessService;

    public function __construct(ForumRoleAccessService $roleAccessService)
    {
        $this->roleAccessService = $roleAccessService;
    }

    public function viewAny(User $user): bool
    {
        // All authenticated users can view threads list
        // But individual threads will be filtered by role access
        return true;
    }

    public function view(User $user, Thread $thread): bool
    {
        // Check role-based access for this thread
        $hasAccess = $this->roleAccessService->canAccessThread($user, $thread->id);
        
        if (!$hasAccess) {
            // Redirect to access denied page
            abort(403, 'Access denied');
        }
        
        return true;
    }

    public function create(User $user): bool
    {
        // Jo user login hai wo thread bana sakta hai
        return true;
    }

    public function update(User $user, Thread $thread): bool
    {
        // Sirf owner hi apne thread ko update kar sakta hai
        return $user->id === $thread->user_id;
    }

    public function delete(User $user, Thread $thread): bool
    {
        // Sirf owner hi apne thread ko delete kar sakta hai
        return $user->id === $thread->user_id;
    }

    public function restore(User $user, Thread $thread): bool
    {
        // Agar restore feature chahiye to true ya ownership check karen
        return false;
    }

    public function forceDelete(User $user, Thread $thread): bool
    {
        // Agar permanent delete chahiye to control karen
        return false;
    }
}
