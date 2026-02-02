<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class StartDiscussionButton extends Component
{
    public string $module;
    public int $itemId;
    public string $itemType;
    public string $itemTitle;
    public ?string $itemUrl;
    public ?int $categoryId;
    public string $forumUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $module,
        int $itemId,
        string $itemType,
        string $itemTitle,
        ?string $itemUrl = null,
        ?int $categoryId = null
    ) {
        $this->module = $module;
        $this->itemId = $itemId;
        $this->itemType = $itemType;
        $this->itemTitle = $itemTitle;
        $this->itemUrl = $itemUrl ?? '#';
        $this->categoryId = $categoryId;
        
        // Build the forum thread creation URL with source module parameters
        $this->buildForumUrl();
    }

    /**
     * Build the forum URL
     */
    protected function buildForumUrl(): void
    {
        // Get default category if not provided
        if (!$this->categoryId) {
            $defaultCategory = \TeamTeaTime\Forum\Models\Category::where('accepts_threads', true)
                ->orderBy('id')
                ->first();
            $this->categoryId = $defaultCategory?->id ?? 1;
        }

        // Get category slug for URL
        $category = \TeamTeaTime\Forum\Models\Category::find($this->categoryId);
        $categorySlug = $category ? Str::slug($category->title) : 'general';
        
        // Build the forum thread creation URL with source module parameters
        // Route pattern: forum/c/{category_id}-{category_slug}/t/create
        try {
            $baseUrl = route('forum.thread.create', [
                'category_id' => $this->categoryId,
                'category_slug' => $categorySlug
            ], false);
        } catch (\Exception $e) {
            // Fallback if route doesn't exist
            $baseUrl = url("/forum/c/{$this->categoryId}-{$categorySlug}/t/create");
        }
        
        $params = http_build_query([
            'source_module' => $this->module,
            'source_item_id' => $this->itemId,
            'source_item_type' => $this->itemType,
            'source_item_title' => $this->itemTitle,
            'source_item_url' => $this->itemUrl,
        ]);
        
        $this->forumUrl = $baseUrl . '?' . $params;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.start-discussion-button');
    }
}
