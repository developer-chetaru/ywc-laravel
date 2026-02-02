<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\BestAnswerService;

class MarkBestAnswer extends Component
{
    public Thread $thread;
    public Post $post;

    protected BestAnswerService $bestAnswerService;

    public function boot(BestAnswerService $bestAnswerService)
    {
        $this->bestAnswerService = $bestAnswerService;
    }

    public function mount(Thread $thread, Post $post)
    {
        $this->thread = $thread;
        $this->post = $post;
    }

    public function markBestAnswer()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to mark best answer.');
            return;
        }

        // Check if thread is a question
        if (!$this->thread->is_question) {
            session()->flash('error', 'This thread must be marked as a question first.');
            return;
        }

        // Check if user is thread author or moderator
        if ($this->thread->author_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            session()->flash('error', 'Only thread author or moderators can mark best answer.');
            return;
        }

        try {
            $this->bestAnswerService->markBestAnswer(Auth::user(), $this->thread, $this->post);
            session()->flash('success', 'Best answer marked! The author received +10 reputation points.');
            
            // Refresh thread to show updated best answer
            $this->thread->refresh();
            $this->dispatch('bestAnswerMarked');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to mark best answer: ' . $e->getMessage());
        }
    }

    public function removeBestAnswer()
    {
        if (!Auth::check()) {
            return;
        }

        if ($this->thread->author_id !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            session()->flash('error', 'Only thread author or moderators can remove best answer.');
            return;
        }

        try {
            $this->bestAnswerService->removeBestAnswer(Auth::user(), $this->thread);
            session()->flash('success', 'Best answer removed.');
            
            $this->thread->refresh();
            $this->dispatch('bestAnswerRemoved');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove best answer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $isBestAnswer = $this->bestAnswerService->isBestAnswer($this->post);
        $canMark = Auth::check() && 
                   $this->thread->is_question && 
                   ($this->thread->author_id === Auth::id() || Auth::user()->hasRole('super_admin'));

        return view('livewire.forum.mark-best-answer', [
            'isBestAnswer' => $isBestAnswer,
            'canMark' => $canMark,
        ]);
    }
}
