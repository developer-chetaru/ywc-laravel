<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TeamTeaTime\Forum\Models\Thread;
use App\Models\User;

class NewThreadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Thread $thread;
    public User $threadAuthor;

    /**
     * Create a new message instance.
     */
    public function __construct(Thread $thread, User $threadAuthor)
    {
        $this->thread = $thread;
        $this->threadAuthor = $threadAuthor;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $threadUrl = route('forum.thread.show', [
            'thread_id' => $this->thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($this->thread->title),
        ]);

        return $this->subject("New Thread: {$this->thread->title}")
            ->view('emails.forum.new-thread', [
                'thread' => $this->thread,
                'threadAuthor' => $this->threadAuthor,
                'threadUrl' => $threadUrl,
            ]);
    }
}
