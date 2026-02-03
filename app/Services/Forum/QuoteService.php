<?php

namespace App\Services\Forum;

use App\Models\User;
use TeamTeaTime\Forum\Models\Post;
use App\Models\ForumPostQuote;
use App\Services\Forum\ForumNotificationService;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    /**
     * Quote a post in a reply
     * 
     * @param Post $quotedPost The post being quoted
     * @param string $replyContent The full reply content (may contain quote tags)
     * @param Post $newPost The new post containing the quote
     * @return void
     */
    public function processQuotes(Post $quotedPost, string $replyContent, Post $newPost): void
    {
        // Check if the reply content contains a quote reference to this post
        // Quote format: [quote=post_id]...[/quote] or <quote post="post_id">...</quote>
        $postId = $quotedPost->id;
        
        // Check for [quote=post_id] format
        $pattern1 = '/\[quote=' . $postId . '\](.*?)\[\/quote\]/is';
        // Check for <quote post="post_id"> format
        $pattern2 = '/<quote\s+post=["\']?' . $postId . '["\']?>(.*?)<\/quote>/is';
        
        if (preg_match($pattern1, $replyContent, $matches) || preg_match($pattern2, $replyContent, $matches)) {
            $quotedContent = $matches[1] ?? '';
            
            // Create quote record
            ForumPostQuote::firstOrCreate(
                [
                    'quoted_post_id' => $quotedPost->id,
                    'quoting_post_id' => $newPost->id,
                ],
                [
                    'quoted_content' => $this->truncateContent($quotedContent, 500),
                ]
            );

            // Send notification to quoted post author
            $quotedAuthor = User::find($quotedPost->author_id);
            if ($quotedAuthor && $quotedAuthor->id !== $newPost->author_id) {
                $notificationService = app(ForumNotificationService::class);
                $thread = $quotedPost->thread;
                $notificationService->notifyQuote($quotedAuthor, $thread, $quotedPost, User::find($newPost->author_id));
            }
        }
    }

    /**
     * Format post content with quote blocks
     * 
     * @param string $content
     * @return string
     */
    public function formatQuotes(string $content): string
    {
        // Handle nested quotes by processing from innermost to outermost
        // Use a placeholder system to prevent re-processing
        $placeholders = [];
        $placeholderIndex = 0;
        
        // First pass: Replace all quotes with placeholders and store them
        while (preg_match('/\[quote=(\d+)\](.*?)\[\/quote\]/is', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $placeholder = '___QUOTE_PLACEHOLDER_' . $placeholderIndex . '___';
            $placeholders[$placeholder] = $matches[0][0];
            $content = str_replace($matches[0][0], $placeholder, $content);
            $placeholderIndex++;
        }
        
        // Second pass: Process placeholders from last to first (outermost to innermost)
        // This ensures nested quotes are processed correctly
        krsort($placeholders);
        
        foreach ($placeholders as $placeholder => $quoteTag) {
            if (preg_match('/\[quote=(\d+)\](.*?)\[\/quote\]/is', $quoteTag, $matches)) {
                $postId = $matches[1];
                $quotedText = $matches[2];
                $post = Post::with(['author', 'thread'])->find($postId);
                
                if ($post && $post->author && $post->thread) {
                    $authorName = $post->author->first_name . ' ' . $post->author->last_name;
                    $postUrl = route('forum.thread.show', [
                        'thread_id' => $post->thread_id,
                        'thread_slug' => \Illuminate\Support\Str::slug($post->thread->title),
                    ]) . '#post-' . $postId;
                    
                    // Recursively format any nested quotes in the quoted text
                    $formattedQuotedText = $this->formatQuotes($quotedText);
                    
                    // Check if content is HTML (from rich text editor)
                    $isHtml = strip_tags($formattedQuotedText) !== $formattedQuotedText;
                    
                    $formattedQuote = sprintf(
                        '<blockquote class="border-l-4 border-blue-500 pl-4 py-2 my-4 bg-blue-50 rounded-r">
                            <div class="text-sm text-gray-600 mb-2">
                                <a href="%s" class="font-semibold text-blue-600 hover:text-blue-800">%s</a> wrote:
                            </div>
                            <div class="text-gray-800">%s</div>
                        </blockquote>',
                        $postUrl,
                        e($authorName),
                        $isHtml ? $formattedQuotedText : nl2br(e($formattedQuotedText))
                    );
                    
                    $content = str_replace($placeholder, $formattedQuote, $content);
                } else {
                    // Post not found, return original
                    $content = str_replace($placeholder, $quoteTag, $content);
                }
            }
        }

        // Convert <quote post="post_id">...</quote> format (if any)
        $content = preg_replace_callback(
            '/<quote\s+post=["\']?(\d+)["\']?>(.*?)<\/quote>/is',
            function ($matches) {
                $postId = $matches[1];
                $quotedText = $matches[2];
                $post = Post::with(['author', 'thread'])->find($postId);
                
                if ($post && $post->author && $post->thread) {
                    $authorName = $post->author->first_name . ' ' . $post->author->last_name;
                    $postUrl = route('forum.thread.show', [
                        'thread_id' => $post->thread_id,
                        'thread_slug' => \Illuminate\Support\Str::slug($post->thread->title),
                    ]) . '#post-' . $postId;
                    
                    // Check if content is HTML (from rich text editor)
                    $isHtml = strip_tags($quotedText) !== $quotedText;
                    
                    return sprintf(
                        '<blockquote class="border-l-4 border-blue-500 pl-4 py-2 my-4 bg-blue-50 rounded-r">
                            <div class="text-sm text-gray-600 mb-2">
                                <a href="%s" class="font-semibold text-blue-600 hover:text-blue-800">%s</a> wrote:
                            </div>
                            <div class="text-gray-800">%s</div>
                        </blockquote>',
                        $postUrl,
                        e($authorName),
                        $isHtml ? $quotedText : nl2br(e($quotedText))
                    );
                }
                
                return $matches[0];
            },
            $content
        );

        return $content;
    }

    /**
     * Get all quotes for a post
     * 
     * @param Post $post
     * @return \Illuminate\Support\Collection
     */
    public function getQuotesForPost(Post $post)
    {
        return ForumPostQuote::where('quoted_post_id', $post->id)
            ->with(['quotingPost.author', 'quotingPost.thread'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all posts that quoted a specific post
     * 
     * @param Post $post
     * @return \Illuminate\Support\Collection
     */
    public function getPostsQuoting(Post $post)
    {
        return ForumPostQuote::where('quoted_post_id', $post->id)
            ->with(['quotingPost.author', 'quotingPost.thread'])
            ->get()
            ->pluck('quotingPost');
    }

    /**
     * Truncate content to specified length
     * 
     * @param string $content
     * @param int $length
     * @return string
     */
    public function truncateContent(string $content, int $length = 500): string
    {
        $content = strip_tags($content);
        if (strlen($content) <= $length) {
            return $content;
        }
        return substr($content, 0, $length) . '...';
    }
}
