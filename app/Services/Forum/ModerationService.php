<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Services\Forum\ForumReputationService;

class ModerationService
{
    protected ForumReputationService $reputationService;

    public function __construct(ForumReputationService $reputationService)
    {
        $this->reputationService = $reputationService;
    }

    // Report reasons
    const REASON_SPAM = 'spam';
    const REASON_HARASSMENT = 'harassment';
    const REASON_OFF_TOPIC = 'off_topic';
    const REASON_INAPPROPRIATE = 'inappropriate';
    const REASON_LIBEL = 'libel';
    const REASON_PRIVACY = 'privacy';

    /**
     * Create a report
     * 
     * @param User $reporter
     * @param string $reportableType 'thread' or 'post'
     * @param int $reportableId
     * @param string $reason
     * @param string $explanation
     * @return int Report ID
     */
    public function createReport(User $reporter, string $reportableType, int $reportableId, string $reason, string $explanation): int
    {
        // Validate reportable type
        if (!in_array($reportableType, ['thread', 'post'])) {
            throw new \InvalidArgumentException("Invalid reportable type: {$reportableType}");
        }

        // Check if user already reported this item
        $existingReport = DB::table('forum_reports')
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->where('reporter_id', $reporter->id)
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            throw new \Exception('You have already reported this item.');
        }

        $reportId = DB::table('forum_reports')->insertGetId([
            'reportable_type' => $reportableType,
            'reportable_id' => $reportableId,
            'reporter_id' => $reporter->id,
            'reason' => $reason,
            'explanation' => $explanation,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log moderation action
        $this->logModerationAction(
            $reporter, // Reporter acts as moderator for logging
            'report',
            $reportableType,
            $reportableId,
            "Reported for: {$reason}"
        );

        return $reportId;
    }

    /**
     * Get pending reports
     * 
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPendingReports(int $limit = 50)
    {
        return DB::table('forum_reports')
            ->where('status', 'pending')
            ->join('users as reporters', 'forum_reports.reporter_id', '=', 'reporters.id')
            ->select(
                'forum_reports.*',
                'reporters.first_name as reporter_first_name',
                'reporters.last_name as reporter_last_name'
            )
            ->orderBy('forum_reports.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Resolve a report
     * 
     * @param User $moderator
     * @param int $reportId
     * @param string $action 'resolved' or 'dismissed'
     * @param string|null $notes
     * @return bool
     */
    public function resolveReport(User $moderator, int $reportId, string $action, ?string $notes = null): bool
    {
        if (!in_array($action, ['resolved', 'dismissed'])) {
            throw new \InvalidArgumentException("Invalid action: {$action}");
        }

        $report = DB::table('forum_reports')->where('id', $reportId)->first();
        if (!$report) {
            return false;
        }

        DB::table('forum_reports')
            ->where('id', $reportId)
            ->update([
                'status' => $action,
                'moderator_id' => $moderator->id,
                'moderator_notes' => $notes,
                'resolved_at' => now(),
                'updated_at' => now(),
            ]);

        return true;
    }

    /**
     * Issue a warning to a user
     * 
     * @param User $moderator
     * @param User $user
     * @param string $reason
     * @param string|null $policyCitation
     * @return int Warning ID
     */
    public function issueWarning(User $moderator, User $user, string $reason, ?string $policyCitation = null): int
    {
        $warningId = DB::table('forum_warnings')->insertGetId([
            'user_id' => $user->id,
            'moderator_id' => $moderator->id,
            'reason' => $reason,
            'policy_citation' => $policyCitation,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Deduct reputation for warning
        $this->reputationService->deductWarning($user, $reason);

        // Send notification
        $notificationService = app(\App\Services\Forum\ForumNotificationService::class);
        $notificationService->notifyModeration(
            $user,
            'warn',
            "You received a warning: {$reason}" . ($policyCitation ? " (Policy: {$policyCitation})" : '')
        );

        // Log moderation action
        $this->logModerationAction($moderator, 'warn', 'user', $user->id, $reason);

        // Check if user should be banned (3 warnings = 7 day ban, 5 warnings = permanent ban)
        $warningCount = DB::table('forum_warnings')
            ->where('user_id', $user->id)
            ->count();

        if ($warningCount >= 5) {
            $this->banUser($moderator, $user, 'permanent', null, 'Accumulated 5 warnings');
        } elseif ($warningCount >= 3) {
            $this->banUser($moderator, $user, 'temporary', 7, 'Accumulated 3 warnings');
        }

        return $warningId;
    }

    /**
     * Get user's warning count
     * 
     * @param User $user
     * @return int
     */
    public function getUserWarningCount(User $user): int
    {
        return DB::table('forum_warnings')
            ->where('user_id', $user->id)
            ->count();
    }

    /**
     * Ban a user
     * 
     * @param User $moderator
     * @param User $user
     * @param string $type 'temporary' or 'permanent'
     * @param int|null $durationDays For temporary bans
     * @param string $reason
     * @return int Ban ID
     */
    public function banUser(User $moderator, User $user, string $type, ?int $durationDays = null, string $reason = ''): int
    {
        if (!in_array($type, ['temporary', 'permanent', 'ip'])) {
            throw new \InvalidArgumentException("Invalid ban type: {$type}");
        }

        $expiresAt = null;
        if ($type === 'temporary' && $durationDays) {
            $expiresAt = now()->addDays($durationDays);
        }

        // Deactivate any existing bans for this user
        DB::table('forum_bans')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $banId = DB::table('forum_bans')->insertGetId([
            'user_id' => $user->id,
            'moderator_id' => $moderator->id,
            'type' => $type,
            'duration_days' => $durationDays,
            'reason' => $reason,
            'expires_at' => $expiresAt,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send notification
        $notificationService = app(\App\Services\Forum\ForumNotificationService::class);
        $banMessage = $type === 'permanent' 
            ? "Your account has been permanently banned. Reason: {$reason}"
            : "Your account has been temporarily banned for {$durationDays} days. Reason: {$reason}";
        $notificationService->notifyModeration($user, 'ban', $banMessage);

        // Log moderation action
        $this->logModerationAction($moderator, 'ban', 'user', $user->id, $reason);

        return $banId;
    }

    /**
     * Unban a user
     * 
     * @param User $moderator
     * @param User $user
     * @return bool
     */
    public function unbanUser(User $moderator, User $user): bool
    {
        DB::table('forum_bans')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

        // Log moderation action
        // Send notification
        $notificationService = app(\App\Services\Forum\ForumNotificationService::class);
        $notificationService->notifyModeration($user, 'unban', 'Your account ban has been removed.');

        $this->logModerationAction($moderator, 'unban', 'user', $user->id, 'Ban removed');

        return true;
    }

    /**
     * Check if user is banned
     * 
     * @param User $user
     * @return bool
     */
    public function isUserBanned(User $user): bool
    {
        return DB::table('forum_bans')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Lock a thread
     * 
     * @param User $moderator
     * @param Thread $thread
     * @param string|null $reason
     * @return bool
     */
    public function lockThread(User $moderator, Thread $thread, ?string $reason = null): bool
    {
        $thread->update(['locked' => true]);

        $this->logModerationAction($moderator, 'lock', 'thread', $thread->id, $reason);

        return true;
    }

    /**
     * Unlock a thread
     * 
     * @param User $moderator
     * @param Thread $thread
     * @return bool
     */
    public function unlockThread(User $moderator, Thread $thread): bool
    {
        $thread->update(['locked' => false]);

        $this->logModerationAction($moderator, 'unlock', 'thread', $thread->id);

        return true;
    }

    /**
     * Pin a thread
     * 
     * @param User $moderator
     * @param Thread $thread
     * @return bool
     */
    public function pinThread(User $moderator, Thread $thread): bool
    {
        $thread->update(['pinned' => true]);

        $this->logModerationAction($moderator, 'pin', 'thread', $thread->id);

        return true;
    }

    /**
     * Unpin a thread
     * 
     * @param User $moderator
     * @param Thread $thread
     * @return bool
     */
    public function unpinThread(User $moderator, Thread $thread): bool
    {
        $thread->update(['pinned' => false]);

        $this->logModerationAction($moderator, 'unpin', 'thread', $thread->id);

        return true;
    }

    /**
     * Move a thread to different category
     * 
     * @param User $moderator
     * @param Thread $thread
     * @param int $newCategoryId
     * @param string|null $reason
     * @return bool
     */
    public function moveThread(User $moderator, Thread $thread, int $newCategoryId, ?string $reason = null): bool
    {
        $oldCategoryId = $thread->category_id;
        $thread->update(['category_id' => $newCategoryId]);

        $this->logModerationAction(
            $moderator,
            'move',
            'thread',
            $thread->id,
            $reason,
            ['old_category_id' => $oldCategoryId, 'new_category_id' => $newCategoryId]
        );

        return true;
    }

    /**
     * Delete a post (soft delete)
     * 
     * @param User $moderator
     * @param Post $post
     * @param string|null $reason
     * @return bool
     */
    public function deletePost(User $moderator, Post $post, ?string $reason = null): bool
    {
        $post->delete(); // Soft delete

        $this->logModerationAction($moderator, 'delete', 'post', $post->id, $reason);

        return true;
    }

    /**
     * Delete a thread (soft delete)
     * 
     * @param User $moderator
     * @param Thread $thread
     * @param string|null $reason
     * @return bool
     */
    public function deleteThread(User $moderator, Thread $thread, ?string $reason = null): bool
    {
        $thread->delete(); // Soft delete

        $this->logModerationAction($moderator, 'delete', 'thread', $thread->id, $reason);

        return true;
    }

    /**
     * Log a moderation action
     * 
     * @param User $moderator
     * @param string $action
     * @param string $targetType
     * @param int $targetId
     * @param string|null $reason
     * @param array|null $metadata
     * @return void
     */
    protected function logModerationAction(User $moderator, string $action, string $targetType, int $targetId, ?string $reason = null, ?array $metadata = null): void
    {
        DB::table('forum_moderation_logs')->insert([
            'moderator_id' => $moderator->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'reason' => $reason,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get moderation statistics
     * 
     * @return array
     */
    public function getModerationStats(): array
    {
        return [
            'pending_reports' => DB::table('forum_reports')->where('status', 'pending')->count(),
            'resolved_reports_today' => DB::table('forum_reports')
                ->where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
            'active_bans' => DB::table('forum_bans')->where('is_active', true)->count(),
            'warnings_today' => DB::table('forum_warnings')
                ->whereDate('created_at', today())
                ->count(),
        ];
    }
}
