<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use App\Models\User;

class QuoteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Thread $thread;
    public Post $post;
    public User $quoter;

    /**
     * Create a new message instance.
     */
    public function __construct(Thread $thread, Post $post, User $quoter)
    {
        $this->thread = $thread;
        $this->post = $post;
        $this->quoter = $quoter;
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

        return $this->subject("You Were Quoted: {$this->thread->title}")
            ->view('emails.forum.quote', [
                'thread' => $this->thread,
                'post' => $this->post,
                'quoter' => $this->quoter,
                'threadUrl' => $threadUrl,
            ]);
    }
}
