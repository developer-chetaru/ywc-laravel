<?php

/**
 * Bootstrap file to fix TesseractOCR namespace exec() bug
 * 
 * This file must be loaded BEFORE the TesseractOCR library is instantiated.
 * It creates an exec() function in the thiagoalessio\TesseractOCR namespace
 * that delegates to the global exec() function or uses alternatives if exec() is disabled.
 */

namespace thiagoalessio\TesseractOCR;

if (!function_exists('thiagoalessio\TesseractOCR\exec')) {
    /**
     * Wrapper for global exec() function to fix namespace bug
     * Falls back to shell_exec or proc_open if exec() is disabled
     * 
     * @param string $command
     * @param array|null $output
     * @param int|null $return_var
     * @return string|false
     */
    function exec($command, &$output = null, &$return_var = null)
    {
        // Check if global exec() is available
        if (function_exists('\exec')) {
            return \exec($command, $output, $return_var);
        }
        
        // Fallback to shell_exec if exec() is disabled
        if (function_exists('\shell_exec')) {
            $result = \shell_exec($command . ' 2>&1');
            if ($output !== null) {
                $output = explode("\n", trim($result));
            }
            if ($return_var !== null) {
                // shell_exec doesn't return exit code, assume success if output exists
                $return_var = $result !== null ? 0 : 1;
            }
            return $result;
        }
        
        // Last resort: use proc_open
        if (function_exists('\proc_open')) {
            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            
            $process = \proc_open($command, $descriptorspec, $pipes);
            
            if (is_resource($process)) {
                \fclose($pipes[0]);
                
                $stdout = stream_get_contents($pipes[1]);
                \fclose($pipes[1]);
                
                $stderr = stream_get_contents($pipes[2]);
                \fclose($pipes[2]);
                
                $returnCode = \proc_close($process);
                
                if ($output !== null) {
                    $output = explode("\n", trim($stdout));
                }
                if ($return_var !== null) {
                    $return_var = $returnCode;
                }
                
                return $stdout;
            }
        }
        
        // If all methods fail, throw an exception
        throw new \RuntimeException(
            'exec(), shell_exec(), and proc_open() are all disabled on this server. ' .
            'Please contact your server administrator to enable at least one of these functions ' .
            'for Tesseract OCR to work.'
        );
    }
}
