<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\QuoteService;

class QuotePost extends Component
{
    public Post $post;
    public bool $showQuoteModal = false;
    public string $quotedContent = '';

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function openQuoteModal()
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to quote posts.');
            return;
        }

        // Get the post content to quote
        $this->quotedContent = strip_tags($this->post->content);
        $this->showQuoteModal = true;
    }

    public function closeQuoteModal()
    {
        $this->showQuoteModal = false;
        $this->quotedContent = '';
    }

    public function insertQuote()
    {
        if (!Auth::check()) {
            return;
        }

        // Format the quote for insertion
        $quoteText = $this->quotedContent;
        $authorName = $this->post->author->first_name . ' ' . $this->post->author->last_name;
        
        // Create quote tag format: [quote=post_id]content[/quote]
        $quoteTag = sprintf(
            "[quote=%d]%s[/quote]\n\n",
            $this->post->id,
            $quoteText
        );

        // Dispatch event to insert quote into reply textarea
        $this->dispatch('insert-quote', quote: $quoteTag);
        
        $this->closeQuoteModal();
    }

    public function render()
    {
        return view('livewire.forum.quote-post');
    }
}
