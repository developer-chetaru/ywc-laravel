<div class="border border-gray-200 bg-gray-50 rounded-md p-6 mb-4">
    @php
        $quoteService = app(\App\Services\Forum\QuoteService::class);
        $content = $post->content;
        
        // Strip HTML tags if content contains HTML
        if (strip_tags($content) !== $content) {
            $content = strip_tags($content);
            $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $content = preg_replace('/\s+/', ' ', $content);
            $content = trim($content);
        }
        
        $formattedContent = $quoteService->formatQuotes($content);
        
        // Always convert markdown to HTML (bold, italic, etc.)
        $formattedContent = \App\Helpers\MarkdownHelper::toHtml($formattedContent);
    @endphp
    {!! $formattedContent !!}
    <div class="flex mt-4">
        <div class="grow">
            <span class="font-medium">
                {{ $post->authorName }}
            </span>
            <span class="text-slate-500">
                <livewire:forum::components.timestamp :carbon="$post->updated_at" />
            </span>
        </div>
        <div>
            <a href="{{ Forum::route('thread.show', $post) }}">#{{ $post->sequence }}</a>
        </div>
    </div>
</div>
