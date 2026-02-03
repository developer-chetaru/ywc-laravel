<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ModerationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $action;
    public $reason;
    public $link;

    public function __construct(string $action, string $reason, ?string $link = null)
    {
        $this->action = $action;
        $this->reason = $reason;
        $this->link = $link ?? route('forum.category.index');
    }

    public function build()
    {
        $actionLabels = [
            'warn' => 'Warning Issued',
            'ban' => 'Account Banned',
            'unban' => 'Account Unbanned',
            'post_deleted' => 'Post Deleted',
            'thread_deleted' => 'Thread Deleted',
            'post_edited' => 'Post Edited by Moderator',
        ];

        $subject = $actionLabels[$this->action] ?? 'Moderation Action';

        return $this->subject($subject)
            ->view('emails.forum.moderation', [
                'action' => $this->action,
                'reason' => $this->reason,
                'link' => $this->link,
            ]);
    }
}
