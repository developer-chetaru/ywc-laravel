<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 my-4">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $thread->title }}</h1>

    <div class="prose max-w-none text-gray-900">
        @php
            // Get the first post (sequence 0) or use firstPost relationship if available
            $firstPost = $thread->firstPost ?? $thread->posts()->where('sequence', 0)->first();
        @endphp
        
        @if($firstPost)
            @php
                $quoteService = app(\App\Services\Forum\QuoteService::class);
                $content = $firstPost->content;
                
                // Strip HTML tags if content contains HTML (from old posts or Quill editor)
                // Convert HTML entities and clean up
                if (strip_tags($content) !== $content) {
                    // Content has HTML tags, strip them and convert to plain text
                    $content = strip_tags($content);
                    // Decode HTML entities
                    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    // Clean up extra whitespace
                    $content = preg_replace('/\s+/', ' ', $content);
                    $content = trim($content);
                }
                
                $formattedContent = $quoteService->formatQuotes($content);
                
                // Always convert markdown to HTML (bold, italic, etc.)
                $formattedContent = \App\Helpers\MarkdownHelper::toHtml($formattedContent);
            @endphp
            {!! $formattedContent !!}
        @else
            <p class="text-gray-500 italic">No content available for this thread.</p>
        @endif
    </div>

    <div class="mt-6 text-sm text-gray-600 border-t border-gray-200 pt-4">
        Posted by <span class="font-medium text-gray-800">{{ $thread->author->name ?? 'Unknown' }}</span> 
        on <span class="font-medium">{{ $thread->created_at->format('M d, Y H:i') }}</span>
    </div>
</div>
