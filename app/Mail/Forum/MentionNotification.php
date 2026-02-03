<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use App\Models\User;

class MentionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $thread;
    public $post;
    public $mentioner;

    public function __construct(Thread $thread, Post $post, User $mentioner)
    {
        $this->thread = $thread;
        $this->post = $post;
        $this->mentioner = $mentioner;
    }

    public function build()
    {
        $url = route('forum.thread.show', [
            'thread_id' => $this->thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($this->thread->title),
        ]) . '#post-' . $this->post->id;

        return $this->subject("You were mentioned in: {$this->thread->title}")
            ->view('emails.forum.mention', [
                'thread' => $this->thread,
                'post' => $this->post,
                'mentioner' => $this->mentioner,
                'url' => $url,
            ]);
    }
}
