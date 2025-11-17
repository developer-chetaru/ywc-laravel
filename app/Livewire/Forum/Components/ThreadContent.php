<?php

namespace App\Livewire\Forum\Components;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;

class ThreadContent extends Component
{
    public Thread $thread;
    public $posts;

    protected $listeners = ['postAdded' => 'loadPosts'];

    public function mount(Thread $thread)
    {
        // Eager load firstPost and author relationships
        $this->thread = $thread->loadMissing(['firstPost', 'author', 'posts' => function($query) {
            $query->where('sequence', 0)->limit(1);
        }]);
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $this->posts = Post::where('thread_id', $this->thread->id)
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.forum.components.thread-content');
    }
}