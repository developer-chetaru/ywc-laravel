<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\ForumReactionService;

class PostReactions extends Component
{
    public Post $post;
    public array $reactionCounts = [];
    public ?string $userReaction = null;

    protected ForumReactionService $reactionService;

    public function boot(ForumReactionService $reactionService)
    {
        $this->reactionService = $reactionService;
    }

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->loadReactions();
    }

    public function loadReactions()
    {
        $this->reactionCounts = $this->reactionService->getReactionCounts($this->post);
        $this->userReaction = $this->reactionService->getUserReaction(Auth::user(), $this->post);
    }

    public function toggleReaction(string $reactionType)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to react to posts.');
            return;
        }

        try {
            $result = $this->reactionService->toggleReaction(Auth::user(), $this->post, $reactionType);
            
            // Reload reactions
            $this->loadReactions();

            // Show success message if reputation was awarded
            if ($result['reputation_awarded']) {
                session()->flash('success', 'Reaction added! The author received +3 reputation points.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add reaction: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.forum.post-reactions');
    }
}
