<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Services\Forum\ForumReputationService;
use App\Services\Forum\ForumNotificationService;

class ForumReactionService
{
    protected ForumReputationService $reputationService;

    public function __construct(ForumReputationService $reputationService)
    {
        $this->reputationService = $reputationService;
    }

    /**
     * Available reaction types
     */
    const REACTION_LIKE = 'like';
    const REACTION_HELPFUL = 'helpful';
    const REACTION_INSIGHTFUL = 'insightful';

    /**
     * Toggle reaction on a post
     * 
     * @param User $user
     * @param Post $post
     * @param string $reactionType
     * @return array
     */
    public function toggleReaction(User $user, Post $post, string $reactionType): array
    {
        // Validate reaction type
        if (!in_array($reactionType, [self::REACTION_LIKE, self::REACTION_HELPFUL, self::REACTION_INSIGHTFUL])) {
            throw new \InvalidArgumentException("Invalid reaction type: {$reactionType}");
        }

        return DB::transaction(function () use ($user, $post, $reactionType) {
            // Check if user already has this reaction
            $existingReaction = DB::table('forum_post_reactions')
                ->where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->where('reaction_type', $reactionType)
                ->first();

            if ($existingReaction) {
                // Remove reaction
                DB::table('forum_post_reactions')
                    ->where('id', $existingReaction->id)
                    ->delete();

                $action = 'removed';
                $reputationAwarded = false;
            } else {
                // Remove any other reaction from this user on this post (only one reaction per post)
                DB::table('forum_post_reactions')
                    ->where('post_id', $post->id)
                    ->where('user_id', $user->id)
                    ->delete();

                // Add new reaction
                DB::table('forum_post_reactions')->insert([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'reaction_type' => $reactionType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $action = 'added';

                // Award reputation for helpful reaction (only to post author, not reactor)
                if ($reactionType === self::REACTION_HELPFUL && $post->author_id !== $user->id) {
                    $postAuthor = User::find($post->author_id);
                    if ($postAuthor) {
                        $this->reputationService->awardHelpfulReaction($postAuthor, $post->id);
                        // Badge checking happens inside reputation service
                        $reputationAwarded = true;
                    } else {
                        $reputationAwarded = false;
                    }
                } else {
                    $reputationAwarded = false;
                }
                
                // Send notification to post author (if not reacting to own post)
                if ($post->author_id !== $user->id) {
                    $postAuthor = User::find($post->author_id);
                    if ($postAuthor) {
                        $notificationService = app(ForumNotificationService::class);
                        $notificationService->notifyReaction($postAuthor, $post, $user, $reactionType);
                    }
                }
            }

            // Get updated reaction counts
            $reactionCounts = $this->getReactionCounts($post);
            $userReaction = $this->getUserReaction($user, $post);

            return [
                'action' => $action,
                'reaction_type' => $reactionType,
                'reaction_counts' => $reactionCounts,
                'user_reaction' => $userReaction,
                'reputation_awarded' => $reputationAwarded,
            ];
        });
    }

    /**
     * Get reaction counts for a post
     * 
     * @param Post $post
     * @return array
     */
    public function getReactionCounts(Post $post): array
    {
        $counts = DB::table('forum_post_reactions')
            ->where('post_id', $post->id)
            ->select('reaction_type', DB::raw('count(*) as count'))
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();

        return [
            'like' => $counts[self::REACTION_LIKE] ?? 0,
            'helpful' => $counts[self::REACTION_HELPFUL] ?? 0,
            'insightful' => $counts[self::REACTION_INSIGHTFUL] ?? 0,
        ];
    }

    /**
     * Get user's reaction on a post
     * 
     * @param User $user
     * @param Post $post
     * @return string|null
     */
    public function getUserReaction(?User $user, Post $post): ?string
    {
        if (!$user) {
            return null;
        }

        $reaction = DB::table('forum_post_reactions')
            ->where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->value('reaction_type');

        return $reaction;
    }

    /**
     * Get all reactions for a post with user details
     * 
     * @param Post $post
     * @return \Illuminate\Support\Collection
     */
    public function getPostReactions(Post $post)
    {
        return DB::table('forum_post_reactions')
            ->where('post_id', $post->id)
            ->join('users', 'forum_post_reactions.user_id', '=', 'users.id')
            ->select(
                'forum_post_reactions.reaction_type',
                'forum_post_reactions.created_at',
                'users.id as user_id',
                'users.first_name',
                'users.last_name'
            )
            ->orderBy('forum_post_reactions.created_at', 'desc')
            ->get();
    }
}
