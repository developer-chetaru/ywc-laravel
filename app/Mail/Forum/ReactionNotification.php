<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TeamTeaTime\Forum\Models\Post;
use App\Models\User;

class ReactionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Post $post;
    public User $reactor;
    public string $reactionType;

    /**
     * Create a new message instance.
     */
    public function __construct(Post $post, User $reactor, string $reactionType)
    {
        $this->post = $post;
        $this->reactor = $reactor;
        $this->reactionType = $reactionType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $thread = $this->post->thread;
        $threadUrl = route('forum.thread.show', [
            'thread_id' => $thread->id,
            'thread_slug' => \Illuminate\Support\Str::slug($thread->title),
        ]) . '#post-' . $this->post->id;

        $reactionLabels = [
            'like' => 'liked',
            'helpful' => 'found helpful',
            'insightful' => 'found insightful',
        ];

        $reactionLabel = $reactionLabels[$this->reactionType] ?? 'reacted to';

        return $this->subject("Your Post Was {$reactionLabel}")
            ->view('emails.forum.reaction', [
                'post' => $this->post,
                'reactor' => $this->reactor,
                'reactionType' => $this->reactionType,
                'reactionLabel' => $reactionLabel,
                'threadUrl' => $threadUrl,
            ]);
    }
}
