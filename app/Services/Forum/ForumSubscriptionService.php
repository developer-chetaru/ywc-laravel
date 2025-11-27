<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Thread;
use Illuminate\Support\Facades\DB;

class ForumSubscriptionService
{
    /**
     * Subscribe a user to a thread (mark as read to enable notifications)
     */
    public function subscribeUserToThread(User $user, Thread $thread): void
    {
        // Check if already subscribed
        $exists = DB::table('forum_threads_read')
            ->where('thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            DB::table('forum_threads_read')->insert([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Subscribe all users to main community thread
     */
    public function subscribeAllUsersToMainThread(): int
    {
        $mainThreadId = config('forum.main_thread_id');
        
        if (!$mainThreadId) {
            throw new \Exception('Main thread ID not configured. Run MainCommunityThreadSeeder first.');
        }

        $thread = Thread::find($mainThreadId);
        if (!$thread) {
            throw new \Exception('Main thread not found.');
        }

        $users = User::where('is_active', true)->get();
        $subscribed = 0;

        foreach ($users as $user) {
            try {
                $this->subscribeUserToThread($user, $thread);
                $subscribed++;
            } catch (\Exception $e) {
                // Log error but continue
                \Log::warning("Failed to subscribe user {$user->id}: " . $e->getMessage());
            }
        }

        return $subscribed;
    }

    /**
     * Auto-subscribe new user to main thread
     */
    public function subscribeNewUser(User $user): void
    {
        $mainThreadId = config('forum.main_thread_id');
        
        if ($mainThreadId) {
            $thread = Thread::find($mainThreadId);
            if ($thread) {
                $this->subscribeUserToThread($user, $thread);
            }
        }
    }
}

