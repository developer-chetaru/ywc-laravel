<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;

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

        // Create reply
        $post = Post::create([
            'thread_id' => $this->thread->id,
            'author_id' => Auth::id(),
            'content'   => $this->content,
        ]);

        // Reset textarea
        $this->reset('content');

        // 🔥 Tell parent/listener to refresh
        $this->dispatch('postAdded', $post->id);
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
