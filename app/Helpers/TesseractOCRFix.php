<?php

/**
 * Bootstrap file to fix TesseractOCR namespace exec() bug
 * 
 * This file must be loaded BEFORE the TesseractOCR library is instantiated.
 * It creates an exec() function in the thiagoalessio\TesseractOCR namespace
 * that delegates to the global exec() function.
 */

namespace thiagoalessio\TesseractOCR;

if (!function_exists('thiagoalessio\TesseractOCR\exec')) {
    /**
     * Wrapper for global exec() function to fix namespace bug
     * 
     * @param string $command
     * @param array|null $output
     * @param int|null $return_var
     * @return string|false
     */
    function exec($command, &$output = null, &$return_var = null)
    {
        return \exec($command, $output, $return_var);
    }
}
