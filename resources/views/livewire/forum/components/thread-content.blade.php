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
                $formattedContent = $quoteService->formatQuotes($firstPost->content);
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
