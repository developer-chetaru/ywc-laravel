<?php

namespace App\Services\Documents;

use thiagoalessio\TesseractOCR\TesseractOCR;

/**
 * Wrapper for TesseractOCR to fix namespace exec() issue
 * 
 * The thiagoalessio/tesseract_ocr library has a bug where it calls exec()
 * without the global namespace prefix, causing "Call to undefined function"
 * errors. This wrapper ensures the executable path is set before any
 * internal exec() calls are made.
 */
class TesseractOCRWrapper
{
    protected $tesseractPath;
    protected $ocr;

    public function __construct($imagePath, $tesseractPath)
    {
        $this->tesseractPath = $tesseractPath;
        
        // Set environment variable that might be checked by library
        putenv("TESSERACT_CMD={$tesseractPath}");
        
        // CRITICAL: Create function alias in library's namespace to fix exec() namespace bug
        // The library calls exec() without \ prefix, so PHP looks for thiagoalessio\TesseractOCR\exec()
        // We create an alias that points to the global exec() function
        if (!function_exists('thiagoalessio\TesseractOCR\exec')) {
            // Use eval to create function in library's namespace
            // This is a workaround for the library's namespace bug
            eval('namespace thiagoalessio\TesseractOCR; function exec() { return \exec(...func_get_args()); }');
        }
        
        // Create the OCR instance
        // The library might call exec() during construction, so we need to catch that
        try {
            $this->ocr = new TesseractOCR($imagePath);
            
            // Immediately set executable path to prevent library from calling exec()
            $this->ocr->executable($tesseractPath);
        } catch (\Error $e) {
            // If we get the namespace exec() error, provide helpful message
            if (strpos($e->getMessage(), 'exec()') !== false || 
                strpos($e->getMessage(), 'undefined function') !== false) {
                throw new \RuntimeException(
                    "TesseractOCR library error: {$e->getMessage()}. " .
                    "This is a known library bug. Please ensure:\n" .
                    "1. PHP exec() function is enabled (check php.ini disable_functions)\n" .
                    "2. TESSERACT_PATH is set in .env file: {$tesseractPath}\n" .
                    "3. Tesseract is installed and accessible at: {$tesseractPath}"
                );
            }
            throw $e;
        }
    }

    /**
     * Delegate method calls to the underlying TesseractOCR instance
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->ocr, $method], $args);
    }

    /**
     * Get the underlying TesseractOCR instance
     */
    public function getOCR()
    {
        return $this->ocr;
    }
}
