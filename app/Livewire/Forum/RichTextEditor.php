<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Js;
use Illuminate\Support\Facades\Storage;

class RichTextEditor extends Component
{
    public string $content = '';
    public string $editorId;
    public string $placeholder = 'Write your message...';
    public bool $showPreview = false;
    public string $previewContent = '';

    public function mount(string $editorId = 'rich-editor', string $content = '', string $placeholder = 'Write your message...')
    {
        $this->editorId = $editorId;
        $this->content = $content;
        $this->placeholder = $placeholder;
    }

    public function updatedContent($value)
    {
        // Don't dispatch on every keystroke to prevent flickering
        // The JavaScript will handle the sync
        // This prevents Livewire from re-rendering on every keystroke
    }
    
    public function setContentSilently($content)
    {
        // Set content without triggering updatedContent or re-render
        $this->content = $content;
        // Don't dispatch anything to prevent re-render
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
        if ($this->showPreview) {
            // Convert markdown to HTML for preview
            $this->previewContent = $this->convertMarkdownToHtml($this->content);
        }
    }
    
    protected function convertMarkdownToHtml($markdown)
    {
        // Basic markdown to HTML conversion
        $html = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/<u>(.+?)<\/u>/', '<u>$1</u>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/^(\d+)\. (.+)$/m', '<li>$2</li>', $html);
        $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank">$1</a>', $html);
        $html = preg_replace('/```\n(.+?)\n```/s', '<pre><code>$1</code></pre>', $html);
        $html = preg_replace('/^> (.+)$/m', '<blockquote>$1</blockquote>', $html);
        $html = nl2br($html);
        return $html;
    }

    public function uploadImage($imageData)
    {
        try {
            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
            
            // Generate unique filename
            $filename = 'forum/' . uniqid() . '_' . time() . '.png';
            
            // Store image
            Storage::disk('public')->put($filename, $imageData);
            
            // Return public URL
            $url = Storage::disk('public')->url($filename);
            
            return ['success' => true, 'url' => $url];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    #[On('insert-quote')]
    public function insertQuote($quote)
    {
        $this->dispatch('editor-insert-quote', quote: $quote, editorId: $this->editorId);
    }

    public function render()
    {
        return view('livewire.forum.rich-text-editor');
    }
}
