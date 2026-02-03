<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Services\Forum\ForumReputationService;
use App\Services\Forum\ForumNotificationService;

class BestAnswerService
{
    protected ForumReputationService $reputationService;

    public function __construct(ForumReputationService $reputationService)
    {
        $this->reputationService = $reputationService;
    }

    /**
     * Mark a post as best answer
     * 
     * @param User $user User marking the answer (must be thread author or moderator)
     * @param Thread $thread
     * @param Post $post
     * @return bool
     */
    public function markBestAnswer(User $user, Thread $thread, Post $post): bool
    {
        // Verify post belongs to thread
        if ($post->thread_id !== $thread->id) {
            throw new \InvalidArgumentException('Post does not belong to this thread.');
        }

        // Verify user has permission (thread author or moderator/admin)
        if ($thread->author_id !== $user->id && !$user->hasRole('super_admin')) {
            throw new \UnauthorizedException('Only thread author or moderators can mark best answer.');
        }

        // Verify thread is marked as question
        if (!$thread->is_question) {
            throw new \InvalidArgumentException('Thread must be marked as a question first.');
        }

        return DB::transaction(function () use ($thread, $post) {
            // If there's already a best answer, remove it first
            if ($thread->best_answer_post_id) {
                $oldBestAnswer = Post::find($thread->best_answer_post_id);
                if ($oldBestAnswer) {
                    // Note: We don't remove reputation points from old best answer
                    // This is intentional - they earned it
                }
            }

            // Set new best answer
            $thread->update([
                'best_answer_post_id' => $post->id,
            ]);

            // Award reputation to post author (if not already awarded)
            $postAuthor = User::find($post->author_id);
            if ($postAuthor && $postAuthor->id !== $thread->author_id) {
                // Only award if post author is different from thread author
                // Badge checking happens automatically inside reputation service
                $this->reputationService->awardBestAnswer($postAuthor, $post->id);
                
                // Send notification to post author
                $notificationService = app(ForumNotificationService::class);
                $notificationService->notifyBestAnswer($postAuthor, $thread, $post);
            }

            return true;
        });
    }

    /**
     * Remove best answer
     * 
     * @param User $user
     * @param Thread $thread
     * @return bool
     */
    public function removeBestAnswer(User $user, Thread $thread): bool
    {
        // Verify user has permission
        if ($thread->author_id !== $user->id && !$user->hasRole('super_admin')) {
            throw new \UnauthorizedException('Only thread author or moderators can remove best answer.');
        }

        $thread->update([
            'best_answer_post_id' => null,
        ]);

        return true;
    }

    /**
     * Get best answer for a thread
     * 
     * @param Thread $thread
     * @return Post|null
     */
    public function getBestAnswer(Thread $thread): ?Post
    {
        if (!$thread->best_answer_post_id) {
            return null;
        }

        return Post::find($thread->best_answer_post_id);
    }

    /**
     * Check if a post is the best answer
     * 
     * @param Post $post
     * @return bool
     */
    public function isBestAnswer(Post $post): bool
    {
        $thread = $post->thread;
        return $thread && $thread->best_answer_post_id === $post->id;
    }
}
