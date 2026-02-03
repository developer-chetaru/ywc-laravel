<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6" x-data @editor-content-updated.window="if ($event.detail.editorId === 'quick-reply-editor') { $wire.set('content', $event.detail.content); }">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ trans('forum::general.quick_reply') }}</h2>
    @livewire('forum.rich-text-editor', [
        'editorId' => 'quick-reply-editor',
        'content' => $content,
        'placeholder' => 'Write your reply...'
    ])
    <div class="text-right mt-4">
        <button 
            wire:click="reply"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
            {{ trans('forum::general.reply') }}
        </button>
    </div>
</div>