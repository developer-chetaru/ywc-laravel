<?php

namespace App\Livewire\Forum;

use Livewire\Component;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\Forum\ForumReputationService;
use App\Services\Forum\ForumNotificationService;
use App\Services\Forum\QuoteService;
use App\Services\Forum\HtmlSanitizerService;
use App\Services\Forum\MentionService;

class QuickReply extends Component
{
    public Thread $thread;
    public string $content = '';


    public function reply()
    {
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'You must be logged in to reply.']);
            return;
        }

        // Strip HTML tags and convert to plain text/markdown
        // This ensures content is saved as markdown, not HTML
        $plainContent = strip_tags($this->content);
        
        // Sanitize content (for quote tags and other special formatting)
        $sanitizer = app(HtmlSanitizerService::class);
        $sanitizedContent = $sanitizer->sanitize($plainContent);

        // Calculate sequence number (next post in thread)
        $maxSequence = Post::where('thread_id', $this->thread->id)->max('sequence') ?? 0;
        $nextSequence = $maxSequence + 1;

        // Create reply (save plain text/markdown content with quote tags)
        $post = Post::create([
            'thread_id' => $this->thread->id,
            'author_id' => Auth::id(),
            'content'   => $sanitizedContent, // Save plain text/markdown content with [quote=id] tags
            'sequence'  => $nextSequence,
        ]);

        // Process quotes and send notifications
        $quoteService = app(QuoteService::class);
        // Extract quoted post IDs from content (use sanitized content)
        preg_match_all('/\[quote=(\d+)\](.*?)\[\/quote\]/is', $sanitizedContent, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $index => $quotedPostId) {
                $quotedPost = Post::find($quotedPostId);
                if ($quotedPost) {
                    $quotedContent = $matches[2][$index] ?? '';
                    // Create quote record
                    \App\Models\ForumPostQuote::firstOrCreate(
                        [
                            'quoted_post_id' => $quotedPost->id,
                            'quoting_post_id' => $post->id,
                        ],
                        [
                            'quoted_content' => $quoteService->truncateContent($quotedContent, 500),
                        ]
                    );
                    
                    // Send notification to quoted post author
                    $quotedAuthor = \App\Models\User::find($quotedPost->author_id);
                    if ($quotedAuthor && $quotedAuthor->id !== Auth::id()) {
                        $notificationService = app(ForumNotificationService::class);
                        $thread = $this->thread->fresh(['author', 'category']);
                        $notificationService->notifyQuote($quotedAuthor, $thread, $quotedPost, Auth::user());
                    }
                }
            }
        }

        // Award reputation for posting reply (badge checking happens automatically inside)
        $reputationService = app(ForumReputationService::class);
        $reputationService->awardReplyPosted(Auth::user(), $post->id);

        // Process mentions and send notifications
        $mentionService = app(MentionService::class);
        $mentionedUsers = $mentionService->processMentions($sanitizedContent);
        $notificationService = app(ForumNotificationService::class);
        $thread = $this->thread->fresh(['author', 'category']);
        
        foreach ($mentionedUsers as $mentionedUser) {
            if ($mentionedUser->id !== Auth::id()) {
                $notificationService->notifyMention($mentionedUser, $thread, $post, Auth::user());
            }
        }

        // Send notifications to subscribed users
        $subscribedUsers = \DB::table('forum_threads_read')
            ->where('thread_id', $thread->id)
            ->where('user_id', '!=', Auth::id()) // Don't notify self
            ->pluck('user_id');

        foreach ($subscribedUsers as $userId) {
            $recipient = \App\Models\User::find($userId);
            if ($recipient && !in_array($recipient->id, array_map(fn($u) => $u->id, $mentionedUsers))) {
                $notificationService->notifyNewReply($recipient, $thread, $post, Auth::user());
            }
        }

        // Reset textarea
        $this->reset('content');

        // Dispatch event to refresh the thread posts
        $this->dispatch('postAdded', postId: $post->id);
        
        // Also dispatch a browser event for any JavaScript listeners
        $this->dispatch('post-added', postId: $post->id);
        
        // Show success message
        session()->flash('success', 'Reply posted successfully!');
    }


    // public function reply()
    // {
    //     // Ensure user is logged in
    //     if (!Auth::check()) {
    //         return ['type' => 'error', 'message' => 'You must be logged in to reply.'];
    //     }

    //     // Create reply
    //     $post = Post::create([
    //         'thread_id' => $this->thread->id,
    //         'author_id' => Auth::id(),
    //         'content'   => $this->content,
    //     ]);

    //     // Reset form
    //     $this->reset('content');

    //     return ['type' => 'success', 'message' => 'Reply posted successfully!', 'post_id' => $post->id];
    // }

    public function render()
    {
        return view('livewire.forum.quick-reply');
    }
}
