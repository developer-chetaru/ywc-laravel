<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class WatermarkService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Add watermark to a document image
     * 
     * @param Document $document
     * @param string|null $watermarkText Custom watermark text (defaults to user email)
     * @param string $position Position: 'center', 'bottom-right', 'bottom-left', 'top-right', 'top-left'
     * @param int $opacity Opacity 0-100
     * @return string|null Path to watermarked file or null if failed
     */
    public function addWatermark(
        Document $document,
        ?string $watermarkText = null,
        string $position = 'bottom-right',
        int $opacity = 50
    ): ?string {
        if (!$document->file_path) {
            return null;
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        
        if (!file_exists($filePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Only watermark images (not PDFs for now)
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return null;
        }

        try {
            \Log::info('Starting watermark process for document: ' . $document->id . ' | File: ' . $filePath);
            
            $image = $this->imageManager->read($filePath);
            $user = $document->user;
            
            // Default watermark text
            $text = $watermarkText ?? ($user->email ?? 'YWC Document');
            \Log::info('Watermark text: ' . $text);
            
            // Get image dimensions
            $width = $image->width();
            $height = $image->height();
            \Log::info('Image dimensions: ' . $width . 'x' . $height);
            
            // Calculate watermark position with proper sizing
            // Use a percentage-based font size for better scaling
            $baseFontSize = max(14, min($width, $height) / 30);
            $fontSize = (int)$baseFontSize;
            $padding = max(20, min($width, $height) / 40); // Responsive padding
            
            // Create watermark text with background
            $watermarkX = 0;
            $watermarkY = 0;
            
            // Calculate text dimensions (built-in font 5 is ~8x13 pixels per character)
            $charWidth = 8;
            $charHeight = 13;
            $textWidth = strlen($text) * $charWidth;
            $textHeight = $charHeight;
            
            switch ($position) {
                case 'center':
                    $watermarkX = ($width / 2) - ($textWidth / 2);
                    $watermarkY = ($height / 2) - ($textHeight / 2);
                    break;
                case 'bottom-right':
                    $watermarkX = $width - $textWidth - $padding;
                    $watermarkY = $height - $textHeight - $padding;
                    break;
                case 'bottom-left':
                    $watermarkX = $padding;
                    $watermarkY = $height - $textHeight - $padding;
                    break;
                case 'top-right':
                    $watermarkX = $width - $textWidth - $padding;
                    $watermarkY = $padding;
                    break;
                case 'top-left':
                    $watermarkX = $padding;
                    $watermarkY = $padding;
                    break;
            }
            
            // Add text watermark using Intervention Image v3 API
            try {
                // Use GD functions directly for reliable watermarking
                $gdImage = null;
                
                // Load image based on type
                if ($extension === 'png') {
                    $gdImage = imagecreatefrompng($filePath);
                } elseif (in_array($extension, ['jpg', 'jpeg'])) {
                    $gdImage = imagecreatefromjpeg($filePath);
                } elseif ($extension === 'gif') {
                    $gdImage = imagecreatefromgif($filePath);
                } elseif ($extension === 'webp') {
                    $gdImage = imagecreatefromwebp($filePath);
                }
                
                if ($gdImage) {
                    // Enable alpha blending for transparency
                    imagealphablending($gdImage, true);
                    imagesavealpha($gdImage, true);
                    
                    // Calculate text dimensions (built-in font 5 is ~8x13 pixels per character)
                    $charWidth = 8;
                    $charHeight = 13;
                    $textWidth = strlen($text) * $charWidth;
                    $textHeight = $charHeight;
                    
                    // Background box padding
                    $bgPadding = 10;
                    $bgX1 = (int)$watermarkX - $bgPadding;
                    $bgY1 = (int)$watermarkY - $bgPadding;
                    $bgX2 = (int)$watermarkX + $textWidth + $bgPadding;
                    $bgY2 = (int)$watermarkY + $textHeight + $bgPadding;
                    
                    // Ensure watermark stays within image bounds
                    if ($bgX1 < 0) $bgX1 = 0;
                    if ($bgY1 < 0) $bgY1 = 0;
                    if ($bgX2 > $width) $bgX2 = $width;
                    if ($bgY2 > $height) $bgY2 = $height;
                    
                    // Adjust text position if background was adjusted
                    $watermarkX = $bgX1 + $bgPadding;
                    $watermarkY = $bgY1 + $bgPadding;
                    
                    // Create semi-transparent black background box (professional look)
                    $bgAlpha = (int)(127 * 0.4); // 40% opacity = 60% visible black background
                    $bgColor = imagecolorallocatealpha($gdImage, 0, 0, 0, $bgAlpha);
                    
                    // Draw background box
                    imagefilledrectangle($gdImage, $bgX1, $bgY1, $bgX2, $bgY2, $bgColor);
                    
                    // Add a subtle white border for better definition
                    $borderAlpha = (int)(127 * 0.2);
                    $borderColor = imagecolorallocatealpha($gdImage, 255, 255, 255, $borderAlpha);
                    imagerectangle($gdImage, $bgX1, $bgY1, $bgX2, $bgY2, $borderColor);
                    
                    // Create bright white text color (fully opaque for maximum visibility)
                    $textAlpha = 0; // 0 = fully opaque = 100% visible
                    $textColor = imagecolorallocatealpha($gdImage, 255, 255, 255, $textAlpha);
                    
                    // Use largest built-in font (5) for best visibility
                    $font = 5;
                    
                    // Add text watermark
                    imagestring($gdImage, $font, (int)$watermarkX, (int)$watermarkY, $text, $textColor);
                    
                    \Log::info('Watermark added at position: ' . $watermarkX . ', ' . $watermarkY . ' | Text: ' . $text);
                    
                    // Save watermarked image directly
                    $watermarkedPath = 'watermarked/' . $document->id . '-' . time() . '.' . $extension;
                    $watermarkedFullPath = Storage::disk('public')->path($watermarkedPath);
                    
                    // Ensure directory exists
                    $watermarkedDir = dirname($watermarkedFullPath);
                    if (!is_dir($watermarkedDir)) {
                        mkdir($watermarkedDir, 0755, true);
                    }
                    
                    // Save based on format
                    $saved = false;
                    if ($extension === 'png') {
                        $saved = imagepng($gdImage, $watermarkedFullPath);
                    } elseif (in_array($extension, ['jpg', 'jpeg'])) {
                        $saved = imagejpeg($gdImage, $watermarkedFullPath, 90);
                    } elseif ($extension === 'gif') {
                        $saved = imagegif($gdImage, $watermarkedFullPath);
                    } elseif ($extension === 'webp') {
                        $saved = imagewebp($gdImage, $watermarkedFullPath, 90);
                    }
                    
                    imagedestroy($gdImage);
                    
                    if ($saved && file_exists($watermarkedFullPath)) {
                        \Log::info('Watermarked file saved successfully: ' . $watermarkedPath);
                        return $watermarkedPath;
                    } else {
                        \Log::error('Failed to save watermarked file: ' . $watermarkedFullPath);
                        return null;
                    }
                } else {
                    \Log::warning('Failed to load image for watermarking: ' . $filePath);
                    return null;
                }
            } catch (\Exception $e) {
                \Log::error('Watermark generation failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                return null;
            }
            
            return $watermarkedPath;
        } catch (\Exception $e) {
            \Log::error('Watermark generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add watermark to shared document (for download/view)
     */
    public function addWatermarkToShared(Document $document, ?User $recipient = null): ?string
    {
        $watermarkText = $recipient 
            ? "Shared with: {$recipient->email} | YWC" 
            : "Shared Document | Yacht Workers Council";
            
        // Use higher opacity (70 instead of 40) for better visibility
        return $this->addWatermark($document, $watermarkText, 'bottom-right', 70);
    }

    /**
     * Clean up old watermarked files
     */
    public function cleanupOldWatermarks(int $daysOld = 7): void
    {
        $files = Storage::disk('public')->files('watermarked');
        $cutoffTime = now()->subDays($daysOld)->timestamp;
        
        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            if ($lastModified < $cutoffTime) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
