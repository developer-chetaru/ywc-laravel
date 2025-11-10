<?php

namespace App\Policies;

use App\Models\User;
use TeamTeaTime\Forum\Models\Thread;

class ThreadPolicy
{
    public function viewAny(User $user): bool
    {
        // Sabko threads dikh sakte hain (agar chahiye to true karen)
        return true;
    }

    public function view(User $user, Thread $thread): bool
    {
        // Sab log thread dekh sakte hain
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
