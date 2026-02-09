<?php

namespace App\Services\Forum;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PrivateMessageService
{
    const MAX_MESSAGES_PER_USER = 100;
    const MAX_ATTACHMENT_SIZE = 5242880; // 5MB in bytes

    /**
     * Send a private message
     * 
     * @param User $sender
     * @param User $recipient
     * @param string $subject
     * @param string $content
     * @param int|null $parentMessageId
     * @param array|null $attachments
     * @return int Message ID
     */
    public function sendMessage(
        User $sender,
        User $recipient,
        string $subject,
        string $content,
        ?int $parentMessageId = null,
        ?array $attachments = null
    ): int {
        // Check if recipient has blocked sender
        if ($this->isBlocked($recipient, $sender)) {
            throw new \Exception('You cannot send messages to this user. They have blocked you.');
        }

        // Check if sender has blocked recipient
        if ($this->isBlocked($sender, $recipient)) {
            throw new \Exception('You have blocked this user. Unblock them to send messages.');
        }

        // Check message limit for sender
        $senderMessageCount = $this->getUserMessageCount($sender);
        if ($senderMessageCount >= self::MAX_MESSAGES_PER_USER) {
            throw new \Exception('You have reached the maximum message limit (' . self::MAX_MESSAGES_PER_USER . ' messages).');
        }

        return DB::transaction(function () use ($sender, $recipient, $subject, $content, $parentMessageId, $attachments) {
            // Create message
            $messageId = DB::table('forum_private_messages')->insertGetId([
                'sender_id' => $sender->id,
                'subject' => $subject,
                'content' => $content,
                'parent_message_id' => $parentMessageId,
                'is_read' => false,
                'is_archived' => false,
                'is_deleted_by_sender' => false,
                'is_deleted_by_recipient' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add participants
            DB::table('forum_message_participants')->insert([
                [
                    'message_id' => $messageId,
                    'user_id' => $sender->id,
                    'role' => 'sender',
                    'is_read' => true, // Sender has read their own message
                    'read_at' => now(),
                    'is_archived' => false,
                    'is_deleted' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'message_id' => $messageId,
                    'user_id' => $recipient->id,
                    'role' => 'recipient',
                    'is_read' => false,
                    'read_at' => null, // Recipient hasn't read yet
                    'is_archived' => false,
                    'is_deleted' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // Handle attachments (files are already stored, just save metadata)
            if ($attachments && !empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $this->saveAttachmentMetadata($messageId, $attachment);
                }
            }

            // Send notification to recipient
            try {
                $notificationService = app(\App\Services\Forum\ForumNotificationService::class);
                $notificationService->notifyPrivateMessage($recipient, $messageId, $sender);
            } catch (\Exception $e) {
                // Log error but don't fail the message send
                \Log::error('Failed to send private message notification: ' . $e->getMessage());
            }

            return $messageId;
        });
    }

    /**
     * Get user's inbox messages
     * 
     * @param User $user
     * @param string $folder 'inbox', 'sent', 'archived'
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getUserMessages(User $user, string $folder = 'inbox', int $limit = 50)
    {
        $query = DB::table('forum_private_messages')
            ->join('forum_message_participants', 'forum_private_messages.id', '=', 'forum_message_participants.message_id')
            ->join('users as sender', 'forum_private_messages.sender_id', '=', 'sender.id')
            ->select(
                'forum_private_messages.*',
                'sender.first_name as sender_first_name',
                'sender.last_name as sender_last_name',
                'sender.id as sender_id',
                'forum_message_participants.is_read',
                'forum_message_participants.read_at',
                'forum_message_participants.is_archived',
                'forum_message_participants.is_deleted'
            )
            ->where('forum_message_participants.user_id', $user->id);

        // Filter by folder
        if ($folder === 'inbox') {
            $query->where('forum_message_participants.role', 'recipient')
                  ->where('forum_message_participants.is_deleted', false)
                  ->where('forum_message_participants.is_archived', false)
                  ->where('forum_private_messages.is_deleted_by_recipient', false);
        } elseif ($folder === 'sent') {
            $query->where('forum_message_participants.role', 'sender')
                  ->where('forum_private_messages.is_deleted_by_sender', false)
                  // Join with recipient participant to get recipient info
                  ->leftJoin('forum_message_participants as recipient_participant', function($join) {
                      $join->on('forum_private_messages.id', '=', 'recipient_participant.message_id')
                           ->where('recipient_participant.role', '=', 'recipient');
                  })
                  ->leftJoin('users as recipient', 'recipient_participant.user_id', '=', 'recipient.id')
                  ->addSelect('recipient.first_name as recipient_first_name', 'recipient.last_name as recipient_last_name');
        } elseif ($folder === 'archived') {
            $query->where('forum_message_participants.is_archived', true)
                  ->where('forum_message_participants.is_deleted', false);
        }

        return $query->orderBy('forum_private_messages.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get conversation thread
     * 
     * @param User $user
     * @param int $messageId
     * @return \Illuminate\Support\Collection
     */
    public function getConversation(User $user, int $messageId)
    {
        // Get the root message
        $message = DB::table('forum_private_messages')
            ->where('id', $messageId)
            ->first();

        if (!$message) {
            return collect([]);
        }

        // Find root message (if this is a reply, find the original)
        $rootMessageId = $message->parent_message_id ?? $messageId;
        
        // Get all messages in the conversation thread
        $messages = DB::table('forum_private_messages')
            ->where(function ($query) use ($rootMessageId) {
                $query->where('forum_private_messages.id', $rootMessageId)
                      ->orWhere('forum_private_messages.parent_message_id', $rootMessageId);
            })
            ->join('users as sender', 'forum_private_messages.sender_id', '=', 'sender.id')
            ->leftJoin('forum_message_attachments', 'forum_private_messages.id', '=', 'forum_message_attachments.message_id')
            ->select(
                'forum_private_messages.*',
                'sender.first_name as sender_first_name',
                'sender.last_name as sender_last_name',
                'sender.id as sender_id'
            )
            ->orderBy('forum_private_messages.created_at', 'asc')
            ->get();

        // Mark as read
        $this->markAsRead($user, $messageId);

        return $messages;
    }

    /**
     * Mark message as read
     * 
     * @param User $user
     * @param int $messageId
     * @return bool
     */
    public function markAsRead(User $user, int $messageId): bool
    {
        DB::table('forum_message_participants')
            ->where('message_id', $messageId)
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return true;
    }

    /**
     * Archive message
     * 
     * @param User $user
     * @param int $messageId
     * @return bool
     */
    public function archiveMessage(User $user, int $messageId): bool
    {
        DB::table('forum_message_participants')
            ->where('message_id', $messageId)
            ->where('user_id', $user->id)
            ->update([
                'is_archived' => true,
                'updated_at' => now(),
            ]);

        return true;
    }

    /**
     * Unarchive message
     * 
     * @param User $user
     * @param int $messageId
     * @return bool
     */
    public function unarchiveMessage(User $user, int $messageId): bool
    {
        DB::table('forum_message_participants')
            ->where('message_id', $messageId)
            ->where('user_id', $user->id)
            ->update([
                'is_archived' => false,
                'updated_at' => now(),
            ]);

        return true;
    }

    /**
     * Delete message
     * 
     * @param User $user
     * @param int $messageId
     * @return bool
     */
    public function deleteMessage(User $user, int $messageId): bool
    {
        $message = DB::table('forum_private_messages')
            ->where('id', $messageId)
            ->first();

        if (!$message) {
            return false;
        }

        // Check if user is sender or recipient
        $participant = DB::table('forum_message_participants')
            ->where('message_id', $messageId)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return false;
        }

        if ($participant->role === 'sender') {
            // Mark as deleted by sender
            DB::table('forum_private_messages')
                ->where('id', $messageId)
                ->update(['is_deleted_by_sender' => true]);
        } else {
            // Mark as deleted by recipient
            DB::table('forum_private_messages')
                ->where('id', $messageId)
                ->update(['is_deleted_by_recipient' => true]);
        }

        // Mark participant as deleted
        DB::table('forum_message_participants')
            ->where('message_id', $messageId)
            ->where('user_id', $user->id)
            ->update(['is_deleted' => true]);

        return true;
    }

    /**
     * Block a user
     * 
     * @param User $user
     * @param User $userToBlock
     * @return bool
     */
    public function blockUser(User $user, User $userToBlock): bool
    {
        DB::table('forum_blocked_users')->updateOrInsert(
            [
                'user_id' => $user->id,
                'blocked_user_id' => $userToBlock->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return true;
    }

    /**
     * Unblock a user
     * 
     * @param User $user
     * @param User $userToUnblock
     * @return bool
     */
    public function unblockUser(User $user, User $userToUnblock): bool
    {
        DB::table('forum_blocked_users')
            ->where('user_id', $user->id)
            ->where('blocked_user_id', $userToUnblock->id)
            ->delete();

        return true;
    }

    /**
     * Check if user is blocked
     * 
     * @param User $user
     * @param User $otherUser
     * @return bool
     */
    public function isBlocked(User $user, User $otherUser): bool
    {
        return DB::table('forum_blocked_users')
            ->where('user_id', $user->id)
            ->where('blocked_user_id', $otherUser->id)
            ->exists();
    }

    /**
     * Get unread message count
     * 
     * @param User $user
     * @return int
     */
    public function getUnreadCount(User $user): int
    {
        return DB::table('forum_message_participants')
            ->join('forum_private_messages', 'forum_message_participants.message_id', '=', 'forum_private_messages.id')
            ->where('forum_message_participants.user_id', $user->id)
            ->where('forum_message_participants.role', 'recipient')
            ->where('forum_message_participants.is_read', false)
            ->where('forum_message_participants.is_deleted', false)
            ->where('forum_message_participants.is_archived', false)
            ->where('forum_private_messages.is_deleted_by_recipient', false)
            ->count();
    }

    /**
     * Get user's message count
     * 
     * @param User $user
     * @return int
     */
    protected function getUserMessageCount(User $user): int
    {
        return DB::table('forum_message_participants')
            ->where('user_id', $user->id)
            ->where('role', 'sender')
            ->where('is_deleted', false)
            ->count();
    }

    /**
     * Save attachment metadata (file is already stored)
     * 
     * @param int $messageId
     * @param array $attachment Contains: file (path), name, type, size
     * @return int Attachment ID
     */
    protected function saveAttachmentMetadata(int $messageId, array $attachment): int
    {
        // Validate file size
        if ($attachment['size'] > self::MAX_ATTACHMENT_SIZE) {
            throw new \Exception('File size exceeds maximum limit of 5MB.');
        }

        // File is already stored, just use the path
        $filePath = $attachment['file'];

        if (!$filePath) {
            throw new \Exception('Attachment file path is missing.');
        }

        // Verify file exists
        if (!Storage::disk('public')->exists($filePath)) {
            throw new \Exception('Attachment file not found at: ' . $filePath);
        }

        return DB::table('forum_message_attachments')->insertGetId([
            'message_id' => $messageId,
            'file_name' => $attachment['name'],
            'file_path' => $filePath,
            'file_type' => $attachment['type'] ?? 'application/octet-stream',
            'file_size' => $attachment['size'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
