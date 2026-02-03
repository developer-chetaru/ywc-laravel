<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;

class BestAnswerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Thread $thread;
    public Post $post;

    /**
     * Create a new message instance.
     */
    public function __construct(Thread $thread, Post $post)
    {
        $this->thread = $thread;
        $this->post = $post;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $threadUrl = route('forum.thread.show', [
            'thread_id' => $this->thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($this->thread->title),
        ]) . '#post-' . $this->post->id;

        return $this->subject("Your Post Was Marked as Best Answer: {$this->thread->title}")
            ->view('emails.forum.best-answer', [
                'thread' => $this->thread,
                'post' => $this->post,
                'threadUrl' => $threadUrl,
            ]);
    }
}
