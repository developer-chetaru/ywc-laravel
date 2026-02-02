<?php

namespace App\Services\Forum;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    /**
     * Check and award badges based on user activity
     * 
     * @param User $user
     * @return array Array of newly earned badges
     */
    public function checkAndAwardBadges(User $user): array
    {
        $newlyEarned = [];

        // Get all active badges
        $badges = DB::table('forum_badges')
            ->where('is_active', true)
            ->get();

        foreach ($badges as $badge) {
            // Check if user already has this badge
            $hasBadge = DB::table('forum_user_badges')
                ->where('user_id', $user->id)
                ->where('badge_id', $badge->id)
                ->exists();

            if ($hasBadge) {
                continue; // User already has this badge
            }

            // Check if user meets criteria
            if ($this->meetsCriteria($user, $badge)) {
                $this->awardBadge($user, $badge->id);
                $newlyEarned[] = $badge;
            }
        }

        return $newlyEarned;
    }

    /**
     * Check if user meets badge criteria
     * 
     * @param User $user
     * @param object $badge
     * @return bool
     */
    protected function meetsCriteria(User $user, $badge): bool
    {
        $criteria = json_decode($badge->criteria, true) ?? [];

        // Achievement badges
        if ($badge->type === 'achievement') {
            // Check post count
            if (isset($criteria['posts'])) {
                $postCount = DB::table('forum_posts')
                    ->where('author_id', $user->id)
                    ->count();
                if ($postCount < $criteria['posts']) {
                    return false;
                }
            }

            // Check thread count
            if (isset($criteria['threads'])) {
                $threadCount = DB::table('forum_threads')
                    ->where('author_id', $user->id)
                    ->count();
                if ($threadCount < $criteria['threads']) {
                    return false;
                }
            }

            // Check reputation
            if (isset($criteria['reputation'])) {
                if (($user->forum_reputation_points ?? 0) < $criteria['reputation']) {
                    return false;
                }
            }

            // Check membership years
            if (isset($criteria['years'])) {
                $joinDate = $user->created_at;
                $yearsSinceJoin = $joinDate->diffInYears(now());
                if ($yearsSinceJoin < $criteria['years']) {
                    return false;
                }
            }

            // Check best answers
            if (isset($criteria['best_answers'])) {
                $bestAnswerCount = DB::table('forum_threads')
                    ->join('forum_posts', 'forum_threads.best_answer_post_id', '=', 'forum_posts.id')
                    ->where('forum_posts.author_id', $user->id)
                    ->count();
                if ($bestAnswerCount < $criteria['best_answers']) {
                    return false;
                }
            }

            // Check helpful reactions received
            if (isset($criteria['helpful_reactions'])) {
                $helpfulCount = DB::table('forum_post_reactions')
                    ->join('forum_posts', 'forum_post_reactions.post_id', '=', 'forum_posts.id')
                    ->where('forum_posts.author_id', $user->id)
                    ->where('forum_post_reactions.reaction_type', 'helpful')
                    ->count();
                if ($helpfulCount < $criteria['helpful_reactions']) {
                    return false;
                }
            }
        }

        // Role badges - check if user has the role
        if ($badge->type === 'role') {
            $requiredRole = $criteria['role'] ?? null;
            if ($requiredRole && !$user->hasRole($requiredRole)) {
                return false;
            }
        }

        // Contributor badges
        if ($badge->type === 'contributor') {
            // Check specific contribution metrics
            if (isset($criteria['helpful_reactions_received'])) {
                $count = DB::table('forum_post_reactions')
                    ->join('forum_posts', 'forum_post_reactions.post_id', '=', 'forum_posts.id')
                    ->where('forum_posts.author_id', $user->id)
                    ->where('forum_post_reactions.reaction_type', 'helpful')
                    ->count();
                if ($count < $criteria['helpful_reactions_received']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Award a badge to a user
     * 
     * @param User $user
     * @param int $badgeId
     * @return bool
     */
    public function awardBadge(User $user, int $badgeId): bool
    {
        // Check if already has badge
        $exists = DB::table('forum_user_badges')
            ->where('user_id', $user->id)
            ->where('badge_id', $badgeId)
            ->exists();

        if ($exists) {
            return false;
        }

        DB::table('forum_user_badges')->insert([
            'user_id' => $user->id,
            'badge_id' => $badgeId,
            'earned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Get user's badges
     * 
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function getUserBadges(User $user)
    {
        return DB::table('forum_user_badges')
            ->join('forum_badges', 'forum_user_badges.badge_id', '=', 'forum_badges.id')
            ->where('forum_user_badges.user_id', $user->id)
            ->where('forum_badges.is_active', true)
            ->select(
                'forum_badges.*',
                'forum_user_badges.earned_at'
            )
            ->orderBy('forum_badges.sort_order')
            ->orderBy('forum_user_badges.earned_at', 'desc')
            ->get();
    }

    /**
     * Get badge by name
     * 
     * @param string $name
     * @return object|null
     */
    public function getBadgeByName(string $name)
    {
        return DB::table('forum_badges')
            ->where('name', $name)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Manually assign a badge (for admins)
     * 
     * @param User $user
     * @param int $badgeId
     * @return bool
     */
    public function assignBadge(User $user, int $badgeId): bool
    {
        return $this->awardBadge($user, $badgeId);
    }
}
