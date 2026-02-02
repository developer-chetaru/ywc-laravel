<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\ForumReputationService;

class QuickReply extends Component
{
    public Thread $thread;
    public string $content = '';


    public function reply()
    {
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'You must be logged in to reply.']);
            return;
        }

        // Calculate sequence number (next post in thread)
        $maxSequence = Post::where('thread_id', $this->thread->id)->max('sequence') ?? 0;
        $nextSequence = $maxSequence + 1;

        // Create reply
        $post = Post::create([
            'thread_id' => $this->thread->id,
            'author_id' => Auth::id(),
            'content'   => $this->content,
            'sequence'  => $nextSequence,
        ]);

        // Award reputation for posting reply (badge checking happens automatically inside)
        $reputationService = app(ForumReputationService::class);
        $reputationService->awardReplyPosted(Auth::user(), $post->id);

        // Reset textarea
        $this->reset('content');

        // Dispatch event to refresh the thread posts
        $this->dispatch('postAdded', postId: $post->id);
        
        // Also dispatch a browser event for any JavaScript listeners
        $this->dispatch('post-added', postId: $post->id);
        
        // Show success message
        session()->flash('success', 'Reply posted successfully!');
    }


    // public function reply()
    // {
    //     // Ensure user is logged in
    //     if (!Auth::check()) {
    //         return ['type' => 'error', 'message' => 'You must be logged in to reply.'];
    //     }

    //     // Create reply
    //     $post = Post::create([
    //         'thread_id' => $this->thread->id,
    //         'author_id' => Auth::id(),
    //         'content'   => $this->content,
    //     ]);

    //     // Reset form
    //     $this->reset('content');

    //     return ['type' => 'success', 'message' => 'Reply posted successfully!', 'post_id' => $post->id];
    // }

    public function render()
    {
        return view('livewire.forum.quick-reply');
    }
}
