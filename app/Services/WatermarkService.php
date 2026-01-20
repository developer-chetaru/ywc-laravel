<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use setasign\Fpdi\Fpdi;

class WatermarkService
{
    /**
     * Add watermark to a document
     */
    public function addWatermark(string $filePath, string $watermarkText, array $options = []): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
            return $this->watermarkImage($filePath, $watermarkText, $options);
        } elseif (strtolower($extension) === 'pdf') {
            return $this->watermarkPDF($filePath, $watermarkText, $options);
        }

        // For unsupported formats, return original
        return $filePath;
    }

    /**
     * Add watermark to image
     */
    private function watermarkImage(string $filePath, string $watermarkText, array $options): string
    {
        try {
            $fullPath = storage_path('app/' . $filePath);
            $image = Image::make($fullPath);

            // Calculate dimensions
            $width = $image->width();
            $height = $image->height();
            $fontSize = $options['font_size'] ?? max(20, min($width, $height) / 20);

            // Add semi-transparent background
            $image->rectangle(0, $height - 50, $width, $height, function ($draw) {
                $draw->background('rgba(0, 0, 0, 0.7)');
            });

            // Add watermark text
            $image->text($watermarkText, $width / 2, $height - 25, function ($font) use ($fontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($fontSize);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('middle');
            });

            // Add diagonal watermark
            $image->text($watermarkText, $width / 2, $height / 2, function ($font) use ($fontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($fontSize * 2);
                $font->color('rgba(255, 255, 255, 0.3)');
                $font->align('center');
                $font->valign('middle');
                $font->angle(45);
            });

            // Save watermarked image
            $watermarkedPath = str_replace('.' . pathinfo($filePath, PATHINFO_EXTENSION), '_watermarked.' . pathinfo($filePath, PATHINFO_EXTENSION), $filePath);
            $image->save(storage_path('app/' . $watermarkedPath));

            return $watermarkedPath;
        } catch (\Exception $e) {
            \Log::error('Image watermarking failed: ' . $e->getMessage());
            return $filePath; // Return original on failure
        }
    }

    /**
     * Add watermark to PDF
     */
    private function watermarkPDF(string $filePath, string $watermarkText, array $options): string
    {
        try {
            $fullPath = storage_path('app/' . $filePath);
            $pdf = new Fpdi();

            // Get page count
            $pageCount = $pdf->setSourceFile($fullPath);

            // Process each page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Add watermark text
                $pdf->SetFont('Arial', 'B', 40);
                $pdf->SetTextColor(200, 200, 200);
                $pdf->SetAlpha(0.3);

                // Diagonal watermark
                $pdf->Rotate(45, $size['width'] / 2, $size['height'] / 2);
                $pdf->Text($size['width'] / 3, $size['height'] / 2, $watermarkText);
                $pdf->Rotate(0);

                // Bottom watermark
                $pdf->SetAlpha(0.7);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', '', 10);
                $pdf->Text(10, $size['height'] - 10, $watermarkText . ' - ' . date('Y-m-d H:i:s'));
            }

            // Save watermarked PDF
            $watermarkedPath = str_replace('.pdf', '_watermarked.pdf', $filePath);
            $pdf->Output('F', storage_path('app/' . $watermarkedPath));

            return $watermarkedPath;
        } catch (\Exception $e) {
            \Log::error('PDF watermarking failed: ' . $e->getMessage());
            return $filePath; // Return original on failure
        }
    }

    /**
     * Generate default watermark text
     */
    public function generateWatermarkText($document, $share = null): string
    {
        $text = 'YWC - Confidential';

        if ($share && $share->recipient_email) {
            $text .= ' - ' . $share->recipient_email;
        }

        if ($document && $document->user) {
            $text .= ' - Owned by: ' . $document->user->first_name . ' ' . $document->user->last_name;
        }

        $text .= ' - ' . now()->format('Y-m-d');

        return $text;
    }

    /**
     * Simple text-based watermark for unsupported libraries
     */
    public function addSimpleWatermark(string $content, string $watermarkText): string
    {
        // For text files or as fallback
        return $content . "\n\n" . str_repeat('=', 50) . "\n" . $watermarkText . "\n" . str_repeat('=', 50);
    }
}
