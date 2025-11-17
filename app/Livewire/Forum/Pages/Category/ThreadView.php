<?php

namespace App\Livewire\Forum\Pages\Category;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Actions\CreatePost;
use TeamTeaTime\Forum\Actions\UpdatePost;
use TeamTeaTime\Forum\Actions\DeletePost;
use Illuminate\Support\Facades\Gate;

class ThreadView extends Component
{
    public $threadId;
    public $selectedThread;
    public $replyBody = '';
    public $editPostModal = false;
    public $editPostId = null;
    public $editPostContent = '';

    public function mount($threadId)
    {
        $this->threadId = $threadId;
        $this->loadThread();
    }

    public function loadThread()
    {
        $this->selectedThread = Thread::with([
            'posts' => function($query) {
                $query->orderBy('created_at', 'asc');
            },
            'posts.user:id,first_name,last_name,email,profile_photo_path',
            'category'
        ])->findOrFail($this->threadId);
    }

    public function reply()
    {
        if (empty(trim($this->replyBody))) {
            session()->flash('error', 'Reply cannot be empty.');
            return;
        }

        try {
            $thread = Thread::findOrFail($this->threadId);
            
            if (!Gate::allows('reply', $thread)) {
                session()->flash('error', 'You do not have permission to reply to this thread.');
                return;
            }

            $action = new CreatePost(
                $thread->id,
                auth()->id(),
                trim($this->replyBody)
            );

            $post = $action->execute();

            // Clear reply body
            $this->replyBody = '';

            // Reload thread to show new post
            $this->loadThread();

            session()->flash('success', 'Reply posted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to post reply: ' . $e->getMessage());
        }
    }

    public function openEditPost($postId)
    {
        $post = Post::findOrFail($postId);
        
        if (!Gate::allows('edit', $post)) {
            session()->flash('error', 'You do not have permission to edit this post.');
            return;
        }

        $this->editPostId = $postId;
        $this->editPostContent = $post->content;
        $this->editPostModal = true;
    }

    public function updatePost()
    {
        if (empty(trim($this->editPostContent))) {
            session()->flash('error', 'Post content cannot be empty.');
            return;
        }

        try {
            $post = Post::findOrFail($this->editPostId);
            
            if (!Gate::allows('edit', $post)) {
                session()->flash('error', 'You do not have permission to edit this post.');
                return;
            }

            $action = new UpdatePost($post->id, trim($this->editPostContent));
            $action->execute();

            // Close modal and reload thread
            $this->editPostModal = false;
            $this->editPostId = null;
            $this->editPostContent = '';
            $this->loadThread();

            session()->flash('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update post: ' . $e->getMessage());
        }
    }

    public function deletePost($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            
            if (!Gate::allows('delete', $post)) {
                session()->flash('error', 'You do not have permission to delete this post.');
                return;
            }

            $action = new DeletePost($post->id);
            $action->execute();

            // Reload thread
            $this->loadThread();

            session()->flash('success', 'Post deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete post: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('forum::pages.category.thread-view', [
            'selectedThread' => $this->selectedThread,
        ]);
    }
}

