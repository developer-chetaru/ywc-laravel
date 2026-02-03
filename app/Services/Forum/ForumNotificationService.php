<?php

namespace App\Services\Forum;

use App\Models\User;
use App\Models\ForumNotification;
use App\Models\ForumNotificationPreference;
use App\Mail\Forum\NewReplyNotification;
use App\Mail\Forum\NewThreadNotification;
use App\Mail\Forum\QuoteNotification;
use App\Mail\Forum\ReactionNotification;
use App\Mail\Forum\BestAnswerNotification;
use App\Mail\Forum\PrivateMessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ForumNotificationService
{
    /**
     * Send notification for new reply
     */
    public function notifyNewReply(User $recipient, $thread, $post, $replyAuthor): void
    {
        // Don't notify if user replied to their own thread
        if ($thread->author_id === $recipient->id && $replyAuthor->id === $recipient->id) {
            return;
        }

        // Check if user is subscribed to thread
        $isSubscribed = app(ForumSubscriptionService::class)->isSubscribed($recipient, $thread->id);
        if (!$isSubscribed) {
            return;
        }

        $preference = $this->getPreference($recipient, 'new_reply');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $link = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]) . '#post-' . $post->id;

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'new_reply',
            'title' => 'New Reply',
            'message' => "{$replyAuthor->first_name} {$replyAuthor->last_name} replied to your thread: {$thread->title}",
            'link' => $link,
            'data' => [
                'thread_id' => $thread->id,
                'post_id' => $post->id,
                'author_id' => $replyAuthor->id,
                'author_name' => "{$replyAuthor->first_name} {$replyAuthor->last_name}",
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new NewReplyNotification($thread, $post, $replyAuthor));
            } catch (\Exception $e) {
                Log::error('Failed to send new reply notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification for new thread in subscribed category
     */
    public function notifyNewThread(User $recipient, $thread, $threadAuthor): void
    {
        // Don't notify if user created the thread
        if ($threadAuthor->id === $recipient->id) {
            return;
        }

        $preference = $this->getPreference($recipient, 'new_thread');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $link = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]);

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'new_thread',
            'title' => 'New Thread',
            'message' => "{$threadAuthor->first_name} {$threadAuthor->last_name} created a new thread: {$thread->title}",
            'link' => $link,
            'data' => [
                'thread_id' => $thread->id,
                'author_id' => $threadAuthor->id,
                'author_name' => "{$threadAuthor->first_name} {$threadAuthor->last_name}",
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new NewThreadNotification($thread, $threadAuthor));
            } catch (\Exception $e) {
                Log::error('Failed to send new thread notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification for quote
     */
    public function notifyQuote(User $recipient, $thread, $post, $quoter): void
    {
        // Don't notify if user quoted themselves
        if ($quoter->id === $recipient->id) {
            return;
        }

        $preference = $this->getPreference($recipient, 'quote');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $link = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]) . '#post-' . $post->id;

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'quote',
            'title' => 'You Were Quoted',
            'message' => "{$quoter->first_name} {$quoter->last_name} quoted your post in: {$thread->title}",
            'link' => $link,
            'data' => [
                'thread_id' => $thread->id,
                'post_id' => $post->id,
                'author_id' => $quoter->id,
                'author_name' => "{$quoter->first_name} {$quoter->last_name}",
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new QuoteNotification($thread, $post, $quoter));
            } catch (\Exception $e) {
                Log::error('Failed to send quote notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification for reaction
     */
    public function notifyReaction(User $recipient, $post, $reactor, $reactionType): void
    {
        // Don't notify if user reacted to their own post
        if ($reactor->id === $recipient->id) {
            return;
        }

        $preference = $this->getPreference($recipient, 'reaction');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $thread = $post->thread;
        $link = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]) . '#post-' . $post->id;

        $reactionLabels = [
            'like' => 'liked',
            'helpful' => 'found helpful',
            'insightful' => 'found insightful',
        ];

        $reactionLabel = $reactionLabels[$reactionType] ?? 'reacted to';

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'reaction',
            'title' => 'New Reaction',
            'message' => "{$reactor->first_name} {$reactor->last_name} {$reactionLabel} your post",
            'link' => $link,
            'data' => [
                'post_id' => $post->id,
                'thread_id' => $thread->id,
                'author_id' => $reactor->id,
                'author_name' => "{$reactor->first_name} {$reactor->last_name}",
                'reaction_type' => $reactionType,
            ],
        ]);

        // Send email if enabled (only for helpful reactions to reduce spam)
        if ($preference->email_enabled && $preference->digest_mode === 'none' && $reactionType === 'helpful') {
            try {
                Mail::to($recipient)->send(new ReactionNotification($post, $reactor, $reactionType));
            } catch (\Exception $e) {
                Log::error('Failed to send reaction notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification for best answer
     */
    public function notifyBestAnswer(User $recipient, $thread, $post): void
    {
        $preference = $this->getPreference($recipient, 'best_answer');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $link = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]) . '#post-' . $post->id;

        $threadAuthor = $thread->author;

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'best_answer',
            'title' => 'Best Answer',
            'message' => "Your post was marked as the best answer in: {$thread->title}",
            'link' => $link,
            'data' => [
                'thread_id' => $thread->id,
                'post_id' => $post->id,
                'author_id' => $threadAuthor->id,
                'author_name' => "{$threadAuthor->first_name} {$threadAuthor->last_name}",
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new BestAnswerNotification($thread, $post));
            } catch (\Exception $e) {
                Log::error('Failed to send best answer notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification for private message
     */
    public function notifyPrivateMessage(User $recipient, $messageId, $sender): void
    {
        $preference = $this->getPreference($recipient, 'pm');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $message = DB::table('forum_private_messages')->where('id', $messageId)->first();
        if (!$message) {
            return;
        }

        $link = route('forum.messages.conversation', ['messageId' => $messageId]);

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'pm',
            'title' => 'New Private Message',
            'message' => "You received a new message from {$sender->first_name} {$sender->last_name}",
            'link' => $link,
            'data' => [
                'message_id' => $messageId,
                'sender_id' => $sender->id,
                'sender_name' => "{$sender->first_name} {$sender->last_name}",
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new PrivateMessageNotification($message, $sender));
            } catch (\Exception $e) {
                Log::error('Failed to send private message notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get notification preference for user and type
     */
    protected function getPreference(User $user, string $type): ForumNotificationPreference
    {
        // Ensure mention type is included in valid types
        $validTypes = ['new_reply', 'new_thread', 'quote', 'reaction', 'best_answer', 'pm', 'moderation', 'mention'];
        if (!in_array($type, $validTypes)) {
            $type = 'new_reply'; // Default fallback
        }

        return ForumNotificationPreference::firstOrCreate(
            ['user_id' => $user->id, 'type' => $type],
            [
                'email_enabled' => true,
                'on_site_enabled' => true,
                'digest_mode' => 'none',
            ]
        );
    }

    /**
     * Get unread notification count for user
     */
    public function getUnreadCount(User $user): int
    {
        return ForumNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, User $user): void
    {
        $notification = ForumNotification::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): void
    {
        ForumNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Send notification for mention
     */
    public function notifyMention(User $recipient, $thread, $post, $mentioner): void
    {
        // Don't notify if user mentioned themselves
        if ($mentioner->id === $recipient->id) {
            return;
        }

        $preference = $this->getPreference($recipient, 'mention');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $link = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]) . '#post-' . $post->id;

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'mention',
            'title' => 'You Were Mentioned',
            'message' => "{$mentioner->first_name} {$mentioner->last_name} mentioned you in: {$thread->title}",
            'link' => $link,
            'data' => [
                'thread_id' => $thread->id,
                'post_id' => $post->id,
                'author_id' => $mentioner->id,
                'author_name' => "{$mentioner->first_name} {$mentioner->last_name}",
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new \App\Mail\Forum\MentionNotification($thread, $post, $mentioner));
            } catch (\Exception $e) {
                Log::error('Failed to send mention notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification for moderation action
     */
    public function notifyModeration(User $recipient, string $action, string $reason, ?string $link = null): void
    {
        $preference = $this->getPreference($recipient, 'moderation');
        if (!$preference->on_site_enabled && !$preference->email_enabled) {
            return;
        }

        $actionLabels = [
            'warn' => 'Warning Issued',
            'ban' => 'Account Banned',
            'unban' => 'Account Unbanned',
            'post_deleted' => 'Post Deleted',
            'thread_deleted' => 'Thread Deleted',
            'post_edited' => 'Post Edited by Moderator',
        ];

        $title = $actionLabels[$action] ?? 'Moderation Action';
        $message = $reason ?: "A moderation action was taken on your account: {$action}";

        $notification = ForumNotification::create([
            'user_id' => $recipient->id,
            'type' => 'moderation',
            'title' => $title,
            'message' => $message,
            'link' => $link ?? route('forum.category.index'),
            'data' => [
                'action' => $action,
                'reason' => $reason,
            ],
        ]);

        // Send email if enabled
        if ($preference->email_enabled && $preference->digest_mode === 'none') {
            try {
                Mail::to($recipient)->send(new \App\Mail\Forum\ModerationNotification($action, $reason, $link));
            } catch (\Exception $e) {
                Log::error('Failed to send moderation notification email', [
                    'user_id' => $recipient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send digest emails for users with daily/weekly preferences
     */
    public function sendDigestEmails(string $mode = 'daily'): void
    {
        if (!in_array($mode, ['daily', 'weekly'])) {
            return;
        }

        // Get users who have at least one notification preference with digest mode enabled
        $userIds = \App\Models\ForumNotificationPreference::where('digest_mode', $mode)
            ->where('email_enabled', true)
            ->distinct()
            ->pluck('user_id');

        $users = \App\Models\User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $this->sendUserDigest($user, $mode);
        }
    }

    /**
     * Send digest email for a single user
     */
    protected function sendUserDigest(User $user, string $mode): void
    {
        $startDate = $mode === 'daily' 
            ? now()->subDay() 
            : now()->subWeek();

        // Get unread notifications for this user
        $notifications = ForumNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifications->isEmpty()) {
            return;
        }

        // Group notifications by type
        $grouped = $notifications->groupBy('type');

        try {
            Mail::to($user)->send(new \App\Mail\Forum\DigestNotification($user, $grouped, $mode));
            
            // Mark notifications as read (optional - or keep them unread)
            // ForumNotification::whereIn('id', $notifications->pluck('id'))->update(['is_read' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to send digest email', [
                'user_id' => $user->id,
                'mode' => $mode,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
