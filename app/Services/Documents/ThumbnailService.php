<?php

namespace App\Services\Documents;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Imagick;

class ThumbnailService
{
    protected $imageManager;

    public function __construct()
    {
        // Use GD driver for Intervention Image v3
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Generate thumbnail for a document
     */
    public function generateThumbnail(Document $document): ?string
    {
        if (!$document->file_path) {
            return null;
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        
        if (!file_exists($filePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Handle images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return $this->generateImageThumbnail($filePath, $document);
        }

        // Handle PDFs
        if ($extension === 'pdf') {
            return $this->generatePdfThumbnail($filePath, $document);
        }

        return null;
    }

    /**
     * Generate thumbnail for image files
     */
    protected function generateImageThumbnail(string $filePath, Document $document): ?string
    {
        try {
            $image = $this->imageManager->read($filePath);
            
            // Resize maintaining aspect ratio, max 300x300
            $image->scaleDown(300, 300);
            
            // Encode as JPEG with 85% quality
            $thumbnailData = $image->toJpeg(85);

            $thumbnailPath = 'thumbnails/' . $document->id . '-' . time() . '.jpg';
            Storage::disk('public')->put($thumbnailPath, $thumbnailData);

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Image thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate thumbnail for PDF files (first page)
     */
    protected function generatePdfThumbnail(string $filePath, Document $document): ?string
    {
        try {
            $imagick = new Imagick();
            $imagick->setResolution(150, 150);
            $imagick->readImage($filePath . '[0]'); // First page only
            $imagick->setImageFormat('jpg');
            $imagick->thumbnailImage(300, 300, true, true);
            $imagick->setImageCompressionQuality(85);

            $thumbnailPath = 'thumbnails/' . $document->id . '-' . time() . '.jpg';
            Storage::disk('public')->put($thumbnailPath, $imagick->getImageBlob());

            $imagick->clear();
            $imagick->destroy();

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('PDF thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate thumbnail if not exists
     */
    public function ensureThumbnail(Document $document): void
    {
        if (!$document->thumbnail_path && $document->file_path) {
            $thumbnailPath = $this->generateThumbnail($document);
            if ($thumbnailPath) {
                $document->update(['thumbnail_path' => $thumbnailPath]);
            }
        }
    }

    /**
     * Delete thumbnail
     */
    public function deleteThumbnail(Document $document): void
    {
        if ($document->thumbnail_path) {
            Storage::disk('public')->delete($document->thumbnail_path);
            $document->update(['thumbnail_path' => null]);
        }
    }
}
