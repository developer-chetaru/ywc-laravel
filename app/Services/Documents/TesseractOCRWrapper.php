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
        
        // Ensure the namespace fix is loaded (via composer autoload files)
        // The TesseractOCRFix.php file creates exec() in the library's namespace
        
        // Create the OCR instance
        // The exec() function should now be available in the library's namespace
        try {
            $this->ocr = new TesseractOCR($imagePath);
            
            // CRITICAL: Set executable path IMMEDIATELY after instantiation
            // This prevents the library from trying to call exec() internally to detect path
            // By setting the path explicitly, the library won't need to auto-detect it
            $this->ocr->executable($tesseractPath);
            
        } catch (\Error $e) {
            // Catch fatal errors (like undefined function)
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'exec()') !== false || 
                strpos($errorMsg, 'undefined function') !== false) {
                
                // Check if exec() is available globally
                $execAvailable = function_exists('\exec');
                $shellExecAvailable = function_exists('\shell_exec');
                $procOpenAvailable = function_exists('\proc_open');
                
                // Check if the fix file is being loaded
                $fixFile = app_path('Helpers/TesseractOCRFix.php');
                $fixLoaded = function_exists('thiagoalessio\TesseractOCR\exec');
                
                $diagnostics = [
                    "Error: {$errorMsg}",
                    "TESSERACT_PATH in .env: {$tesseractPath}",
                    "Global exec() available: " . ($execAvailable ? 'YES' : 'NO'),
                    "Global shell_exec() available: " . ($shellExecAvailable ? 'YES' : 'NO'),
                    "Global proc_open() available: " . ($procOpenAvailable ? 'YES' : 'NO'),
                    "Fix file exists: " . (file_exists($fixFile) ? 'YES' : 'NO'),
                    "Fix function loaded: " . ($fixLoaded ? 'YES' : 'NO')
                ];
                
                if (!$execAvailable && !$shellExecAvailable && !$procOpenAvailable) {
                    throw new \RuntimeException(
                        "TesseractOCR cannot work: exec(), shell_exec(), and proc_open() are all disabled.\n\n" .
                        "Please contact your server administrator to enable at least one of these functions.\n\n" .
                        "Diagnostics:\n" . implode("\n", $diagnostics)
                    );
                }
                
                throw new \RuntimeException(
                    "TesseractOCR library error: {$errorMsg}\n\n" .
                    "Diagnostics:\n" . implode("\n", $diagnostics) . "\n\n" .
                    "Please:\n" .
                    "1. Run: composer dump-autoload\n" .
                    "2. Ensure TESSERACT_PATH is set in .env: {$tesseractPath}\n" .
                    "3. If exec() is disabled, ensure shell_exec() or proc_open() is enabled"
                );
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('TesseractOCR creation failed: ' . $e->getMessage());
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
