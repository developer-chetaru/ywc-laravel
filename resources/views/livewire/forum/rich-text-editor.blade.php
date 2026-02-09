@once
<script>
(function() {
    // Store initialized editors to prevent duplicates
    window.__quillEditors = window.__quillEditors || {};
    
    // Helper functions (define once globally)
    if (!window.__quillHelpers) {
        window.__quillHelpers = {
            markdownToHtml(markdown) {
                if (!markdown) return '';
                let html = markdown;
                html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');
                html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
                html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
                html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
                html = html.replace(/^- (.+)$/gm, '<li>$1</li>');
                html = html.replace(/^(\d+)\. (.+)$/gm, '<li>$2</li>');
                html = html.replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>');
                html = html.replace(/```\n(.+?)\n```/gs, '<pre><code>$1</code></pre>');
                html = html.replace(/`(.+?)`/g, '<code>$1</code>');
                html = html.replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2">$1</a>');
                html = html.replace(/\n/g, '<br>');
                return html;
            },
            htmlToMarkdown(html) {
                if (!html) return '';
                let markdown = html;
                markdown = markdown.replace(/<h1>(.*?)<\/h1>/gi, '# $1\n');
                markdown = markdown.replace(/<h2>(.*?)<\/h2>/gi, '## $1\n');
                markdown = markdown.replace(/<h3>(.*?)<\/h3>/gi, '### $1\n');
                markdown = markdown.replace(/<strong>(.*?)<\/strong>/gi, '**$1**');
                markdown = markdown.replace(/<b>(.*?)<\/b>/gi, '**$1**');
                markdown = markdown.replace(/<em>(.*?)<\/em>/gi, '*$1*');
                markdown = markdown.replace(/<i>(.*?)<\/i>/gi, '*$1*');
                markdown = markdown.replace(/<u>(.*?)<\/u>/gi, '$1');
                markdown = markdown.replace(/<s>(.*?)<\/s>/gi, '~~$1~~');
                markdown = markdown.replace(/<ul>(.*?)<\/ul>/gis, '$1');
                markdown = markdown.replace(/<ol>(.*?)<\/ol>/gis, '$1');
                markdown = markdown.replace(/<li>(.*?)<\/li>/gi, '- $1\n');
                markdown = markdown.replace(/<blockquote>(.*?)<\/blockquote>/gis, '> $1\n');
                markdown = markdown.replace(/<pre><code>(.*?)<\/code><\/pre>/gis, '```\n$1\n```');
                markdown = markdown.replace(/<code>(.*?)<\/code>/gi, '`$1`');
                markdown = markdown.replace(/<a href="(.*?)">(.*?)<\/a>/gi, '[$2]($1)');
                markdown = markdown.replace(/<br\s*\/?>/gi, '\n');
                markdown = markdown.replace(/<p>(.*?)<\/p>/gi, '$1\n');
                markdown = markdown.replace(/\n{3,}/g, '\n\n');
                markdown = markdown.trim();
                return markdown;
            }
        };
    }
})();
</script>
@endonce

<div class="rich-text-editor-wrapper" 
     wire:ignore.self
     x-data="{
         quill: null,
         editorId: '{{ $editorId }}',
         isTyping: false,
         updateTimeout: null,
         lastContent: '',
         init() {
             // Prevent duplicate initialization
             if (window.__quillEditors && window.__quillEditors[this.editorId]) {
                 this.quill = window.__quillEditors[this.editorId];
                 return;
             }
             
             // Wait for Quill to be available
             const checkQuill = setInterval(() => {
                 if (typeof Quill !== 'undefined') {
                     clearInterval(checkQuill);
                     this.initializeQuill();
                 }
             }, 100);
             
             // Timeout after 5 seconds
             setTimeout(() => {
                 clearInterval(checkQuill);
                 if (typeof Quill === 'undefined') {
                     console.error('Quill.js failed to load');
                 }
             }, 5000);
         },
         initializeQuill() {
             // Wait for DOM to be ready
             this.$nextTick(() => {
                 const editorElement = document.getElementById(this.editorId + '-editor');
                 if (!editorElement) {
                     console.error('Editor element not found: ' + this.editorId + '-editor');
                     return;
                 }
                 
                 // Check if already initialized
                 if (window.__quillEditors && window.__quillEditors[this.editorId]) {
                     this.quill = window.__quillEditors[this.editorId];
                     return;
                 }
                 
                 // Initialize Quill editor
                 this.quill = new Quill(editorElement, {
                     theme: 'snow',
                     placeholder: '{{ $placeholder }}',
                     modules: {
                         toolbar: [
                             [{ 'header': [1, 2, 3, false] }],
                             ['bold', 'italic', 'underline', 'strike'],
                             [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                             ['blockquote', 'code-block'],
                             ['link'],
                             ['clean']
                         ]
                     }
                 });
                 
                 // Store editor instance
                 if (!window.__quillEditors) {
                     window.__quillEditors = {};
                 }
                 window.__quillEditors[this.editorId] = this.quill;
                 
                 // Set initial content if exists
                 @if($content)
                     try {
                         const content = @json($content);
                         if (content) {
                             this.lastContent = content;
                             const html = window.__quillHelpers.markdownToHtml(content);
                             const delta = this.quill.clipboard.convert({ html: html });
                             this.quill.setContents(delta);
                         }
                     } catch (e) {
                         console.error('Error setting initial content:', e);
                     }
                 @endif
                 
                 // Store content locally, only sync to Livewire when user stops typing
                 this.quill.on('text-change', () => {
                     this.isTyping = true;
                     clearTimeout(this.updateTimeout);
                     
                     // Get current content
                     const html = this.quill.root.innerHTML;
                     const markdown = window.__quillHelpers.htmlToMarkdown(html);
                     
                     // Store locally
                     this.lastContent = markdown;
                     
                     // Only sync to Livewire after user stops typing (debounced)
                     this.updateTimeout = setTimeout(() => {
                         this.isTyping = false;
                         
                         // Dispatch event for parent component
                         window.dispatchEvent(new CustomEvent('editor-content-updated', {
                             detail: { editorId: this.editorId, content: markdown, html: html }
                         }));
                         
                         // Update Livewire only when typing stops (prevents re-render during typing)
                         // Don't update Livewire during typing to prevent re-render
                         // Content will be captured on form submit
                         console.log('Editor content:', markdown);
                     }, 1000); // Increased debounce to 1 second
                 });
                 
                 // Prevent Livewire from morphing this element
                 if (typeof Livewire !== 'undefined') {
                     Livewire.hook('morph.updating', ({ el, component }) => {
                         if (this.isTyping && el && (el.id === this.editorId + '-editor' || el.querySelector('#' + this.editorId + '-editor'))) {
                             return false;
                         }
                     });
                 }
                 
                // Handle Ctrl+Enter to submit
                this.quill.root.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 'Enter') {
                        e.preventDefault();
                        // Save content before submit
                        const html = this.quill.root.innerHTML;
                        const markdown = window.__quillHelpers.htmlToMarkdown(html);
                        if (typeof $wire !== 'undefined') {
                            $wire.set('content', markdown);
                        }
                        window.dispatchEvent(new CustomEvent('editor-submit', {
                            detail: { editorId: this.editorId }
                        }));
                    }
                });
                
                // Listen for quote insertion
                window.addEventListener('editor-insert-quote', (e) => {
                    if (e.detail.editorId === this.editorId && this.quill) {
                        const quote = e.detail.quote || '';
                        if (quote) {
                            // Get current content
                            const currentHtml = this.quill.root.innerHTML;
                            const currentMarkdown = window.__quillHelpers.htmlToMarkdown(currentHtml);
                            
                            // Append quote to current content
                            const newContent = currentMarkdown + (currentMarkdown ? '\n\n' : '') + quote;
                            
                            // Convert to HTML and insert into Quill
                            const html = window.__quillHelpers.markdownToHtml(newContent);
                            const delta = this.quill.clipboard.convert({ html: html });
                            this.quill.setContents(delta);
                            
                            // Update Livewire content
                            if (typeof $wire !== 'undefined') {
                                $wire.set('content', newContent);
                            }
                            
                            // Focus the editor
                            this.quill.focus();
                        }
                    }
                });
             });
         }
     }"
     x-init="init()">
    <div class="border border-gray-300 rounded-lg bg-white" wire:ignore>
        <div id="{{ $editorId }}-editor" class="min-h-[200px]" wire:ignore></div>
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-2 flex justify-between items-center">
            <div class="text-xs text-gray-500">
                <span>Press Ctrl+Enter to submit</span>
            </div>
        </div>
    </div>
</div>
