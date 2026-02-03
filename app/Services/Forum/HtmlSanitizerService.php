<?php

namespace App\Services\Forum;

class HtmlSanitizerService
{
    /**
     * Allowed HTML tags for forum posts
     */
    private array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code',
        'a', 'img', 'video', 'iframe',
        'div', 'span',
        'sub', 'sup',
    ];

    /**
     * Allowed attributes for specific tags
     */
    private array $allowedAttributes = [
        'a' => ['href', 'title', 'target'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'video' => ['src', 'width', 'height', 'controls'],
        'iframe' => ['src', 'width', 'height', 'frameborder', 'allowfullscreen'],
        'div' => ['class'],
        'span' => ['class'],
        'p' => ['class'],
        'code' => ['class'],
        'pre' => ['class'],
    ];

    /**
     * Sanitize HTML content
     */
    public function sanitize(string $html): string
    {
        // Remove script tags and event handlers
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html);
        $html = preg_replace("/on\w+='[^']*'/i", '', $html);
        
        // Use strip_tags with allowed tags
        $html = strip_tags($html, '<' . implode('><', $this->allowedTags) . '>');
        
        // Clean up attributes using DOMDocument
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $xpath = new \DOMXPath($dom);
        
        // Remove all attributes except allowed ones
        foreach ($xpath->query('//*') as $node) {
            $tagName = $node->nodeName;
            
            if (isset($this->allowedAttributes[$tagName])) {
                $allowedAttrs = $this->allowedAttributes[$tagName];
                $attributes = $node->attributes;
                
                for ($i = $attributes->length - 1; $i >= 0; $i--) {
                    $attr = $attributes->item($i);
                    if (!in_array($attr->nodeName, $allowedAttrs)) {
                        $node->removeAttribute($attr->nodeName);
                    } else {
                        // Sanitize attribute values
                        $attrValue = $attr->nodeValue;
                        
                        // For href and src, only allow http, https, and relative URLs
                        if (in_array($attr->nodeName, ['href', 'src'])) {
                            if (!preg_match('/^(https?:\/\/|\/|#)/i', $attrValue)) {
                                $node->removeAttribute($attr->nodeName);
                            }
                        }
                        
                        // Remove javascript: and data: URLs
                        if (preg_match('/^(javascript|data):/i', $attrValue)) {
                            $node->removeAttribute($attr->nodeName);
                        }
                    }
                }
            } else {
                // Remove all attributes if tag not in allowed attributes list
                while ($node->attributes->length > 0) {
                    $node->removeAttribute($node->attributes->item(0)->nodeName);
                }
            }
        }
        
        // Get cleaned HTML
        $html = $dom->saveHTML();
        
        // Remove XML declaration if present
        $html = preg_replace('/<\?xml[^>]*\?>/', '', $html);
        
        // Clean up extra whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        $html = trim($html);
        
        return $html;
    }

    /**
     * Sanitize and clean HTML for display
     */
    public function cleanForDisplay(string $html): string
    {
        $html = $this->sanitize($html);
        
        // Additional cleaning for display
        // Remove empty tags
        $html = preg_replace('/<(\w+)[^>]*>\s*<\/\1>/', '', $html);
        
        return $html;
    }
}
