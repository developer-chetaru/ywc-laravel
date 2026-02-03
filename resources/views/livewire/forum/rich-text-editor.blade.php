<div class="rich-text-editor-wrapper" x-data="{
    formatText(type) {
        const textarea = document.getElementById('{{ $editorId }}');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = textarea.value.substring(start, end);
        let replacement = '';
        if (type === 'bold') replacement = '**' + selected + '**';
        else if (type === 'italic') replacement = '*' + selected + '*';
        else if (type === 'underline') replacement = '<u>' + selected + '</u>';
        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
        textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        $wire.set('content', textarea.value);
    },
    insertMarkdown(markdown) {
        const textarea = document.getElementById('{{ $editorId }}');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        textarea.value = textarea.value.substring(0, start) + markdown + textarea.value.substring(end);
        textarea.focus();
        const newPos = start + markdown.length;
        textarea.setSelectionRange(newPos, newPos);
        $wire.set('content', textarea.value);
    },
    insertLink() {
        const url = prompt('Enter URL:');
        const text = prompt('Enter link text:', url);
        if (url) {
            const textarea = document.getElementById('{{ $editorId }}');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const markdown = '[' + (text || url) + '](' + url + ')';
            textarea.value = textarea.value.substring(0, start) + markdown + textarea.value.substring(end);
            textarea.focus();
            const newPos = start + markdown.length;
            textarea.setSelectionRange(newPos, newPos);
            $wire.set('content', textarea.value);
        }
    }
}">
    <div class="border border-gray-300 rounded-lg bg-white">
        <div class="border-b border-gray-200 bg-gray-50 rounded-t-lg p-2 flex flex-wrap gap-2">
            <button type="button" @click="formatText('bold')" class="px-2 py-1 text-sm font-bold hover:bg-gray-200 rounded" title="Bold">B</button>
            <button type="button" @click="formatText('italic')" class="px-2 py-1 text-sm italic hover:bg-gray-200 rounded" title="Italic">I</button>
            <button type="button" @click="formatText('underline')" class="px-2 py-1 text-sm underline hover:bg-gray-200 rounded" title="Underline">U</button>
            <div class="border-l border-gray-300 mx-1"></div>
            <button type="button" @click="insertMarkdown('# ')" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="Heading">H1</button>
            <button type="button" @click="insertMarkdown('## ')" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="Subheading">H2</button>
            <button type="button" @click="insertMarkdown('- ')" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="List">•</button>
            <button type="button" @click="insertMarkdown('1. ')" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="Numbered List">1.</button>
            <div class="border-l border-gray-300 mx-1"></div>
            <button type="button" @click="insertMarkdown('```\n\n```')" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="Code Block">Code</button>
            <button type="button" @click="insertMarkdown('> ')" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="Quote">Quote</button>
            <button type="button" @click="insertLink()" class="px-2 py-1 text-sm hover:bg-gray-200 rounded" title="Link">Link</button>
        </div>
        <textarea 
            id="{{ $editorId }}"
            wire:model.live="content"
            @input="$wire.set('content', $event.target.value); window.dispatchEvent(new CustomEvent('editor-content-updated', {detail: {editorId: '{{ $editorId }}', content: $event.target.value}}));"
            class="w-full min-h-[200px] max-h-[500px] p-4 border-0 focus:ring-0 focus:outline-none resize-y font-mono text-sm"
            placeholder="{{ $placeholder }}"
        >{{ $content }}</textarea>
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-2 flex justify-between items-center">
            <div class="text-xs text-gray-500">
                <span>Press Ctrl+Enter to submit</span>
            </div>
            <button 
                type="button"
                wire:click="togglePreview"
                class="text-xs text-blue-600 hover:text-blue-800 font-medium"
            >
                <span x-show="!$wire.showPreview">Preview</span>
                <span x-show="$wire.showPreview">Edit</span>
            </button>
        </div>
    </div>
    <div x-show="$wire.showPreview" class="mt-4 border border-gray-300 rounded-lg bg-white p-4 prose prose-sm max-w-none">
        <div class="text-sm text-gray-600 mb-2 font-semibold">Preview:</div>
        <div x-html="$wire.previewContent"></div>
    </div>
</div>
