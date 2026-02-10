<?php

namespace App\Services\Documents;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;

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
        
        // Don't use putenv() - it's often disabled on servers and not essential
        // We'll set the executable path directly via executable() method
        
        // CRITICAL: Create function alias in library's namespace to fix exec() namespace bug
        // The library calls exec() without \ prefix, so PHP looks for thiagoalessio\TesseractOCR\exec()
        // We create an alias that points to the global exec() function
        // Only do this if eval is available and function doesn't exist
        if (function_exists('eval') && !function_exists('thiagoalessio\TesseractOCR\exec')) {
            try {
                // Use eval to create function in library's namespace
                // This is a workaround for the library's namespace bug
                @eval('namespace thiagoalessio\TesseractOCR; function exec() { return \exec(...func_get_args()); }');
            } catch (\Throwable $e) {
                // If eval fails, we'll rely on setting executable path immediately
                // This is fine - the executable() method should prevent internal exec() calls
                Log::debug('Could not create exec() alias in TesseractOCR namespace (eval disabled). Will rely on executable() method.');
            }
        }
        
        // Create the OCR instance
        // The library might call exec() during construction, so we need to catch that
        try {
            // Try to create instance - if it fails due to exec() namespace issue,
            // we'll catch it and provide helpful error
            $this->ocr = new TesseractOCR($imagePath);
            
            // CRITICAL: Set executable path IMMEDIATELY after instantiation
            // This prevents the library from trying to call exec() internally to detect path
            // By setting the path explicitly, the library won't need to auto-detect it
            $this->ocr->executable($tesseractPath);
            
        } catch (\Error $e) {
            // Catch fatal errors (like undefined function)
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'exec()') !== false || 
                strpos($errorMsg, 'undefined function') !== false ||
                strpos($errorMsg, 'putenv()') !== false) {
                
                // This is the namespace bug - library is calling exec() or putenv() in wrong namespace
                throw new \RuntimeException(
                    "TesseractOCR library namespace error: {$errorMsg}\n\n" .
                    "This is a known library bug. Solutions:\n" .
                    "1. Ensure PHP exec() function is enabled (check php.ini disable_functions)\n" .
                    "2. TESSERACT_PATH is set in .env file: {$tesseractPath}\n" .
                    "3. Tesseract is installed and accessible at: {$tesseractPath}\n" .
                    "4. If putenv() is disabled, that's OK - we don't use it\n" .
                    "5. Contact server admin to enable exec() if needed"
                );
            }
            throw $e;
        } catch (\Exception $e) {
            // Re-throw with more context
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'putenv') !== false) {
                throw new \RuntimeException(
                    "putenv() related error: {$errorMsg}. " .
                    "We don't use putenv() anymore. Please ensure TESSERACT_PATH is set in .env: {$tesseractPath}"
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
