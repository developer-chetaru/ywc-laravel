<?php

namespace App\Helpers;

class MarkdownHelper
{
    /**
     * Convert markdown to HTML
     */
    public static function toHtml(string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }
        
        $html = $markdown;
        
        // First, handle code blocks (to prevent processing markdown inside code)
        $codeBlocks = [];
        $html = preg_replace_callback('/```\s*\n?(.+?)\n?```/s', function($matches) use (&$codeBlocks) {
            $placeholder = '___CODE_BLOCK_' . count($codeBlocks) . '___';
            $codeBlocks[$placeholder] = '<pre><code>' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '</code></pre>';
            return $placeholder;
        }, $html);
        
        // Handle inline code
        $html = preg_replace_callback('/`(.+?)`/s', function($matches) {
            return '<code>' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '</code>';
        }, $html);
        
        // Convert headings (must be before other formatting)
        $html = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        
        // Convert bold (**text** or __text__) - handle multiple occurrences
        // Process **bold** - match any characters including spaces (trim whitespace)
        $html = preg_replace_callback('/\*\*([^*]+?)\*\*/', function($matches) {
            return '<strong>' . trim($matches[1]) . '</strong>';
        }, $html);
        // Process __bold__ - match any characters including spaces (trim whitespace)
        $html = preg_replace_callback('/__([^_]+?)__/', function($matches) {
            return '<strong>' . trim($matches[1]) . '</strong>';
        }, $html);
        
        // Convert italic (*text* or _text_) - but not if already bold
        // Only match single * or _ that are not part of ** or __
        $html = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $html);
        $html = preg_replace('/(?<!_)_([^_]+)_(?!_)/', '<em>$1</em>', $html);
        
        // Convert strikethrough (~~text~~)
        $html = preg_replace('/~~(.+?)~~/s', '<s>$1</s>', $html);
        
        // Convert links [text](url)
        $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $html);
        
        // Convert blockquotes (> text)
        $html = preg_replace('/^> (.+)$/m', '<blockquote>$1</blockquote>', $html);
        
        // Convert unordered lists (- item or * item)
        $lines = explode("\n", $html);
        $inList = false;
        $listItems = [];
        $result = [];
        
        foreach ($lines as $line) {
            if (preg_match('/^[-*] (.+)$/', $line, $matches)) {
                if (!$inList) {
                    $inList = true;
                    $listItems = [];
                }
                $listItems[] = '<li>' . trim($matches[1]) . '</li>';
            } else {
                if ($inList) {
                    $result[] = '<ul>' . implode('', $listItems) . '</ul>';
                    $listItems = [];
                    $inList = false;
                }
                $result[] = $line;
            }
        }
        if ($inList) {
            $result[] = '<ul>' . implode('', $listItems) . '</ul>';
        }
        $html = implode("\n", $result);
        
        // Convert ordered lists (1. item)
        $html = preg_replace('/^(\d+)\. (.+)$/m', '<li>$2</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ol>$0</ol>', $html);
        
        // Restore code blocks
        foreach ($codeBlocks as $placeholder => $code) {
            $html = str_replace($placeholder, $code, $html);
        }
        
        // Convert line breaks (but preserve existing HTML)
        $html = preg_replace('/\n(?!<[\/]?(?:h[1-6]|ul|ol|li|blockquote|pre|code))/', '<br>', $html);
        
        // Clean up extra whitespace and line breaks
        $html = preg_replace('/\n{3,}/', "\n\n", $html);
        $html = preg_replace('/(<br>\s*){3,}/', '<br><br>', $html);
        
        return trim($html);
    }
}
