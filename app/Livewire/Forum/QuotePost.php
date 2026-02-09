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

        // Get the post content to quote and clean it properly
        $content = $this->post->content;
        
        // Strip HTML tags if present
        if (strip_tags($content) !== $content) {
            $content = strip_tags($content);
        }
        
        // Decode HTML entities
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove markdown syntax for clean text display in textarea
        // Remove bold markdown (**text** or __text__)
        $content = preg_replace('/\*\*(.*?)\*\*/s', '$1', $content);
        $content = preg_replace('/__(.*?)__/s', '$1', $content);
        
        // Remove italic markdown (*text* or _text_)
        $content = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '$1', $content);
        $content = preg_replace('/(?<!_)_(?!_)(.*?)(?<!_)_(?!_)/s', '$1', $content);
        
        // Remove strikethrough markdown (~~text~~)
        $content = preg_replace('/~~(.*?)~~/s', '$1', $content);
        
        // Remove code markdown (`code` and ```code```)
        $content = preg_replace('/```[\s\S]*?```/s', '', $content);
        $content = preg_replace('/`([^`]+)`/s', '$1', $content);
        
        // Remove link markdown ([text](url))
        $content = preg_replace('/\[([^\]]+)\]\([^\)]+\)/s', '$1', $content);
        
        // Remove zero-width characters and other invisible characters
        $content = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $content);
        
        // Clean up multiple spaces but preserve line breaks
        $content = preg_replace('/[ \t]+/', ' ', $content); // Replace multiple spaces/tabs with single space
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content); // Replace 3+ line breaks with 2
        $content = trim($content);
        
        $this->quotedContent = $content;
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
