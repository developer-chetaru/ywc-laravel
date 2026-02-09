<div>
    <!-- Quote Button -->
    <button 
        wire:click="openQuoteModal"
        class="inline-flex items-center gap-1 px-2 py-1 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
        title="Quote this post"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        Quote
    </button>

    <!-- Quote Modal -->
    @if($showQuoteModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" wire:click="closeQuoteModal">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto" wire:click.stop>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Quote Post</h3>
                    <button 
                        wire:click="closeQuoteModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Original Post by {{ $post->author->first_name }} {{ $post->author->last_name }}
                    </label>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-48 overflow-y-auto">
                        @php
                            // Get original content for display
                            $originalContent = $post->content;
                            
                            // Strip HTML tags if present
                            if (strip_tags($originalContent) !== $originalContent) {
                                $originalContent = strip_tags($originalContent);
                            }
                            
                            // Decode HTML entities
                            $originalContent = html_entity_decode($originalContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            
                            // Remove zero-width characters
                            $originalContent = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $originalContent);
                            
                            // Convert markdown to HTML for display
                            $displayContent = \App\Helpers\MarkdownHelper::toHtml($originalContent);
                        @endphp
                        <div class="text-sm text-gray-700 prose prose-sm max-w-none">{!! $displayContent !!}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select text to quote (or quote entire post)
                    </label>
                    <textarea 
                        wire:model="quotedContent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        rows="6"
                        placeholder="Select the text you want to quote..."
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button 
                        wire:click="closeQuoteModal"
                        class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="insertQuote"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                    >
                        Insert Quote
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
