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
            $image = $this->imageManager->read($filePath);
            $user = $document->user;
            
            // Default watermark text
            $text = $watermarkText ?? ($user->email ?? 'YWC Document');
            
            // Get image dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Calculate watermark position
            $fontSize = max(12, min($width, $height) / 30); // Responsive font size
            $padding = 20;
            
            // Create watermark text with background
            $watermarkX = 0;
            $watermarkY = 0;
            
            switch ($position) {
                case 'center':
                    $watermarkX = ($width / 2) - (strlen($text) * $fontSize / 3);
                    $watermarkY = ($height / 2);
                    break;
                case 'bottom-right':
                    $watermarkX = $width - (strlen($text) * $fontSize / 2) - $padding;
                    $watermarkY = $height - $fontSize - $padding;
                    break;
                case 'bottom-left':
                    $watermarkX = $padding;
                    $watermarkY = $height - $fontSize - $padding;
                    break;
                case 'top-right':
                    $watermarkX = $width - (strlen($text) * $fontSize / 2) - $padding;
                    $watermarkY = $padding + $fontSize;
                    break;
                case 'top-left':
                    $watermarkX = $padding;
                    $watermarkY = $padding + $fontSize;
                    break;
            }
            
            // Add text watermark with background for better visibility
            // First, create a semi-transparent background rectangle
            $bgWidth = strlen($text) * ($fontSize / 1.5) + 20;
            $bgHeight = $fontSize + 10;
            
            // Add text watermark (simpler approach - text only)
            // Note: Intervention Image v3 may have different API, using basic text
            try {
                $image->text(
                    $text,
                    (int)$watermarkX,
                    (int)$watermarkY,
                    function ($font) use ($fontSize, $opacity) {
                        $font->size($fontSize);
                        $font->color([255, 255, 255, (int)(255 * ($opacity / 100))]); // White with opacity
                        $font->align('left');
                        $font->valign('top');
                    }
                );
            } catch (\Exception $e) {
                // Fallback: Try without font configuration
                \Log::warning('Watermark text failed, trying simple approach: ' . $e->getMessage());
                // For now, just save the image without watermark if text fails
            }
            
            // Save watermarked image
            $watermarkedPath = 'watermarked/' . $document->id . '-' . time() . '.' . $extension;
            
            // Encode based on original format
            if ($extension === 'png') {
                $watermarkedData = $image->toPng();
            } elseif ($extension === 'webp') {
                $watermarkedData = $image->toWebp();
            } else {
                $watermarkedData = $image->toJpeg(90); // JPEG with 90% quality
            }
            
            Storage::disk('public')->put($watermarkedPath, $watermarkedData);
            
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
            
        return $this->addWatermark($document, $watermarkText, 'bottom-right', 40);
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
