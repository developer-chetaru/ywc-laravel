<?php

namespace App\Services\Forum;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForumReputationService
{
    // Reputation point values
    const POINTS_THREAD_CREATED = 5;
    const POINTS_REPLY_POSTED = 2;
    const POINTS_HELPFUL_REACTION = 3;
    const POINTS_BEST_ANSWER = 10;
    const POINTS_WARNING = -50;

    // Reputation level thresholds
    const LEVEL_NEWCOMER = 0;
    const LEVEL_ACTIVE_MEMBER = 50;
    const LEVEL_SENIOR_MEMBER = 200;
    const LEVEL_EXPERT = 500;

    /**
     * Add reputation points to a user
     * 
     * @param User $user
     * @param int $points
     * @param string $reason
     * @param string|null $sourceType
     * @param int|null $sourceId
     * @return void
     */
    public function addReputation(User $user, int $points, string $reason, ?string $sourceType = null, ?int $sourceId = null): void
    {
        DB::transaction(function () use ($user, $points, $reason, $sourceType, $sourceId) {
            // Log the reputation change
            DB::table('forum_reputation_logs')->insert([
                'user_id' => $user->id,
                'points' => $points,
                'reason' => $reason,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update user's total reputation points
            $user->increment('forum_reputation_points', $points);

            // Check for new badges after reputation update
            $this->checkBadges($user);
        });
    }

    /**
     * Check and award badges for user
     * 
     * @param User $user
     * @return void
     */
    protected function checkBadges(User $user): void
    {
        try {
            $badgeService = app(\App\Services\Forum\BadgeService::class);
            $newlyEarned = $badgeService->checkAndAwardBadges($user);
            
            // TODO: Send notification for newly earned badges
            // if (!empty($newlyEarned)) {
            //     // Notify user about new badges
            // }
        } catch (\Exception $e) {
            // Log error but don't fail reputation update
            \Log::warning('Failed to check badges for user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Award points for creating a thread
     * 
     * @param User $user
     * @param int $threadId
     * @return void
     */
    public function awardThreadCreated(User $user, int $threadId): void
    {
        $this->addReputation(
            $user,
            self::POINTS_THREAD_CREATED,
            'Thread created',
            'thread',
            $threadId
        );
    }

    /**
     * Award points for posting a reply
     * 
     * @param User $user
     * @param int $postId
     * @return void
     */
    public function awardReplyPosted(User $user, int $postId): void
    {
        $this->addReputation(
            $user,
            self::POINTS_REPLY_POSTED,
            'Reply posted',
            'post',
            $postId
        );
    }

    /**
     * Award points for helpful reaction
     * 
     * @param User $user
     * @param int $postId
     * @return void
     */
    public function awardHelpfulReaction(User $user, int $postId): void
    {
        $this->addReputation(
            $user,
            self::POINTS_HELPFUL_REACTION,
            'Helpful reaction received',
            'reaction',
            $postId
        );
    }

    /**
     * Award points for best answer
     * 
     * @param User $user
     * @param int $postId
     * @return void
     */
    public function awardBestAnswer(User $user, int $postId): void
    {
        $this->addReputation(
            $user,
            self::POINTS_BEST_ANSWER,
            'Best answer selected',
            'best_answer',
            $postId
        );
    }

    /**
     * Deduct points for warning
     * 
     * @param User $user
     * @param string $reason
     * @return void
     */
    public function deductWarning(User $user, string $reason): void
    {
        $this->addReputation(
            $user,
            self::POINTS_WARNING,
            'Warning: ' . $reason,
            'warning',
            null
        );
    }

    /**
     * Get reputation level name based on points
     * 
     * @param int $points
     * @return string
     */
    public function getReputationLevel(int $points): string
    {
        if ($points >= self::LEVEL_EXPERT) {
            return 'Expert';
        } elseif ($points >= self::LEVEL_SENIOR_MEMBER) {
            return 'Senior Member';
        } elseif ($points >= self::LEVEL_ACTIVE_MEMBER) {
            return 'Active Member';
        } else {
            return 'Newcomer';
        }
    }

    /**
     * Get reputation level badge color
     * 
     * @param string $level
     * @return string
     */
    public function getReputationLevelColor(string $level): string
    {
        return match($level) {
            'Expert' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Senior Member' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'Active Member' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    /**
     * Get user's reputation summary
     * 
     * @param User $user
     * @return array
     */
    public function getUserReputationSummary(User $user): array
    {
        $points = $user->forum_reputation_points ?? 0;
        $level = $this->getReputationLevel($points);
        
        // Get recent reputation logs
        $recentLogs = DB::table('forum_reputation_logs')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'points' => $points,
            'level' => $level,
            'level_color' => $this->getReputationLevelColor($level),
            'recent_logs' => $recentLogs,
        ];
    }

    /**
     * Get top contributors (leaderboard)
     * 
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopContributors(int $limit = 10)
    {
        return User::where('forum_reputation_points', '>', 0)
            ->orderBy('forum_reputation_points', 'desc')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'forum_reputation_points'])
            ->map(function ($user) {
                $user->reputation_level = $this->getReputationLevel($user->forum_reputation_points);
                return $user;
            });
    }
}
