<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;

class ThreadPosts extends Component
{
    public Thread $thread;
    public $posts;

    public function mount(Thread $thread)
    {
        $this->posts = $thread->posts()->latest()->get();
    }

    // 🔹 Listen for "reply-posted" event
    protected $listeners = ['reply-posted' => 'refreshPosts'];

    public function refreshPosts($postId)
    {
        // Reload posts so new one appears
        $this->posts = $this->thread->posts()->latest()->get();
    }

    public function render()
    {
        return view('livewire.forum.thread-posts');
    }
}
