<?php

namespace App\Services\Documents;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OcrService
{
    protected $apiKey;
    protected $apiUrl = 'https://vision.googleapis.com/v1/images:annotate';

    public function __construct()
    {
        $this->apiKey = config('services.google_cloud.api_key');
        
        if (!$this->apiKey) {
            Log::warning('Google Cloud Vision API key not configured');
        }
    }

    /**
     * Get Tesseract executable path
     */
    protected function getTesseractPath(): ?string
    {
        // Check if exec function is available
        if (!function_exists('exec') && !function_exists('shell_exec')) {
            return null;
        }

        $output = [];
        $returnVar = 0;
        
        // Method 1: Try direct tesseract --version (most reliable)
        @\exec('tesseract --version 2>&1', $output, $returnVar);
        if ($returnVar === 0) {
            return 'tesseract'; // Use command directly
        }
        
        // Method 2: Try which tesseract
        @\exec('which tesseract 2>&1', $output, $returnVar);
        if ($returnVar === 0 && !empty($output)) {
            $path = trim($output[0]);
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }
        
        // Method 3: Try common installation paths
        $commonPaths = [
            '/usr/bin/tesseract',
            '/usr/local/bin/tesseract',
            '/opt/homebrew/bin/tesseract',
            '/bin/tesseract',
        ];
        
        foreach ($commonPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                @\exec($path . ' --version 2>&1', $output, $returnVar);
                if ($returnVar === 0) {
                    return $path;
                }
            }
        }
        
        // Method 4: Try shell_exec as fallback
        if (function_exists('shell_exec')) {
            $result = @shell_exec('tesseract --version 2>&1');
            if ($result && strpos($result, 'tesseract') !== false) {
                return 'tesseract';
            }
        }
        
        return null;
    }

    /**
     * Create TesseractOCR instance with proper path configuration
     */
    protected function createTesseractOCR($imagePath): \thiagoalessio\TesseractOCR\TesseractOCR
    {
        $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($imagePath);
        
        // Set Tesseract executable path if found (for server environments)
        $tesseractPath = $this->getTesseractPath();
        if ($tesseractPath) {
            // Set executable path explicitly (works on servers where PATH might not include tesseract)
            $ocr->executable($tesseractPath);
        }
        
        return $ocr;
    }

    /**
     * Extract text from document image/PDF using Google Cloud Vision API REST
     */
    public function extractText(string $filePath): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'text' => '',
                'confidence' => 0,
                'message' => 'Google Cloud Vision API key not configured'
            ];
        }

        try {
            // Read file from storage
            $imageContent = Storage::disk('public')->get($filePath);
            
            if (!$imageContent) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'File not found in storage'
                ];
            }
            
            // Check file type - Google Vision API doesn't support PDF directly
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            // For PDFs, fall back to TesseractOCR (Google Vision needs async API for PDFs)
            if ($extension === 'pdf') {
                return $this->extractTextFromPdf($filePath);
            }
            
            // For images, use Google Vision API
            // Encode image to base64
            $base64Image = base64_encode($imageContent);
            
            // Prepare API request
            $requestData = [
                'requests' => [
                    [
                        'image' => [
                            'content' => $base64Image
                        ],
                        'features' => [
                            [
                                'type' => 'DOCUMENT_TEXT_DETECTION',
                                'maxResults' => 1
                            ]
                        ]
                    ]
                ]
            ];
            
            // Call Google Vision API REST endpoint
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, $requestData);
            
            if (!$response->successful()) {
                $error = $response->json();
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'Google Vision API Error: ' . ($error['error']['message'] ?? $response->body())
                ];
            }
            
            $responseData = $response->json();
            
            if (empty($responseData['responses'])) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'No response from Google Vision API'
                ];
            }
            
            $annotateResponse = $responseData['responses'][0];
            
            // Check for errors
            if (isset($annotateResponse['error'])) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'Google Vision API Error: ' . $annotateResponse['error']['message']
                ];
            }
            
            // Get full text annotation
            if (!isset($annotateResponse['fullTextAnnotation'])) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'No text detected in document'
                ];
            }
            
            $fullTextAnnotation = $annotateResponse['fullTextAnnotation'];
            $fullText = $fullTextAnnotation['text'] ?? '';
            
            // Calculate confidence from text annotations
            $avgConfidence = 85.0; // Default confidence
            
            // Try to calculate confidence from pages/blocks
            try {
                if (isset($fullTextAnnotation['pages'])) {
                    $confidences = [];
                    foreach ($fullTextAnnotation['pages'] as $page) {
                        if (isset($page['blocks'])) {
                            foreach ($page['blocks'] as $block) {
                                if (isset($block['paragraphs'])) {
                                    foreach ($block['paragraphs'] as $paragraph) {
                                        if (isset($paragraph['words'])) {
                                            foreach ($paragraph['words'] as $word) {
                                                if (isset($word['symbols'])) {
                                                    foreach ($word['symbols'] as $symbol) {
                                                        if (isset($symbol['confidence']) && $symbol['confidence'] > 0) {
                                                            $confidences[] = $symbol['confidence'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if (!empty($confidences)) {
                        $avgConfidence = (array_sum($confidences) / count($confidences)) * 100;
                    }
                }
            } catch (\Exception $e) {
                Log::debug('Confidence calculation failed, using default', ['error' => $e->getMessage()]);
            }
            
            return [
                'success' => true,
                'text' => $fullText,
                'confidence' => round($avgConfidence, 2),
                'annotations' => $fullTextAnnotation
            ];
            
        } catch (\Exception $e) {
            Log::error('Google Vision API Error: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'text' => '',
                'confidence' => 0,
                'message' => 'OCR processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Detect document structure and extract fields
     */
    public function detectDocumentStructure(string $filePath, string $documentType): array
    {
        $textResult = $this->extractText($filePath);
        
        if (!$textResult['success']) {
            return $textResult;
        }
        
        $text = $textResult['text'];
        $extractedFields = [];
        
        // Document-specific parsing
        switch ($documentType) {
            case 'passport':
                $extractedFields = $this->parsePassport($text);
                break;
            case 'certificate':
                $extractedFields = $this->parseCertificate($text);
                break;
            case 'idvisa':
                $extractedFields = $this->parseIdVisa($text);
                break;
            case 'other':
            default:
                $extractedFields = $this->parseGeneric($text);
        }
        
        return [
            'success' => true,
            'text' => $text,
            'confidence' => $textResult['confidence'],
            'fields' => $extractedFields,
            'overall_confidence' => $this->calculateOverallConfidence($extractedFields)
        ];
    }

    /**
     * Parse passport document (MRZ and VIZ)
     */
    protected function parsePassport(string $text): array
    {
        $fields = [];
        
        // MRZ Pattern (Machine Readable Zone)
        // Format: P<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<<<<<<<<<<<
        if (preg_match('/P<[A-Z]{3}([A-Z<]+)<<([A-Z<]+)/', $text, $matches)) {
            $fields['surname'] = str_replace('<', ' ', trim($matches[1]));
            $fields['given_names'] = str_replace('<', ' ', trim($matches[2]));
        }
        
        // Alternative: Look for name patterns
        if (empty($fields['surname']) && preg_match('/SURNAME[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['surname'] = trim($matches[1]);
        }
        if (empty($fields['given_names']) && preg_match('/GIVEN[:\s]+NAMES?[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['given_names'] = trim($matches[1]);
        }
        
        // Passport number (usually 6-9 alphanumeric characters)
        if (preg_match('/PASSPORT[:\s]+(?:NO|NUMBER|#)[:\s]*([A-Z0-9]{6,9})/i', $text, $matches)) {
            $fields['passport_number'] = $matches[1];
        } elseif (preg_match('/\b([A-Z]{1,2}[0-9]{6,9})\b/', $text, $matches)) {
            $fields['passport_number'] = $matches[1];
        }
        
        // Date of birth (DDMMYY format in MRZ or various formats)
        if (preg_match('/DATE[:\s]+OF[:\s]+BIRTH[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['dob'] = $this->normalizeDate($matches[1]);
        } elseif (preg_match('/DOB[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['dob'] = $this->normalizeDate($matches[1]);
        }
        
        // Expiry date
        if (preg_match('/EXPIR[YIES]+[:\s]+(?:DATE|ON)[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['expiry_date'] = $this->normalizeDate($matches[1]);
        } elseif (preg_match('/VALID[:\s]+UNTIL[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['expiry_date'] = $this->normalizeDate($matches[1]);
        }
        
        // Issue date
        if (preg_match('/ISSUE[D]?[:\s]+(?:DATE|ON)[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['issue_date'] = $this->normalizeDate($matches[1]);
        }
        
        // Nationality
        if (preg_match('/NATIONALITY[:\s]+([A-Z]{3})/i', $text, $matches)) {
            $fields['nationality'] = $matches[1];
        } elseif (preg_match('/COUNTRY[:\s]+OF[:\s]+BIRTH[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['nationality'] = trim($matches[1]);
        }
        
        return $fields;
    }

    /**
     * Parse certificate document
     */
    protected function parseCertificate(string $text): array
    {
        $fields = [];
        
        // Certificate number
        if (preg_match('/CERTIFICATE[:\s]+(?:NO|NUMBER|#)[:\s]*([A-Z0-9\-]+)/i', $text, $matches)) {
            $fields['certificate_number'] = trim($matches[1]);
        } elseif (preg_match('/CERT[:\s]+(?:NO|NUMBER|#)[:\s]*([A-Z0-9\-]+)/i', $text, $matches)) {
            $fields['certificate_number'] = trim($matches[1]);
        } elseif (preg_match('/\b([A-Z]{2,4}[0-9]{4,10})\b/', $text, $matches)) {
            $fields['certificate_number'] = $matches[1];
        }
        
        // Certificate type/name
        if (preg_match('/CERTIFICATE[:\s]+OF[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['certificate_type'] = trim($matches[1]);
        } elseif (preg_match('/STCW[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['certificate_type'] = 'STCW ' . trim($matches[1]);
        }
        
        // Issue date
        if (preg_match('/ISSUE[D]?[:\s]+(?:DATE|ON)[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['issue_date'] = $this->normalizeDate($matches[1]);
        } elseif (preg_match('/DATE[:\s]+OF[:\s]+ISSUE[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['issue_date'] = $this->normalizeDate($matches[1]);
        }
        
        // Expiry date
        if (preg_match('/EXPIR[YIES]+[:\s]+(?:DATE|ON)[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['expiry_date'] = $this->normalizeDate($matches[1]);
        } elseif (preg_match('/VALID[:\s]+UNTIL[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['expiry_date'] = $this->normalizeDate($matches[1]);
        } elseif (preg_match('/UNLIMITED/i', $text)) {
            $fields['expiry_date'] = null; // Unlimited validity
        }
        
        // Issuing authority
        if (preg_match('/ISSUED[:\s]+BY[:\s]+([A-Z\s&\.]+)/i', $text, $matches)) {
            $fields['issuing_authority'] = trim($matches[1]);
        } elseif (preg_match('/ISSUING[:\s]+AUTHORITY[:\s]+([A-Z\s&\.]+)/i', $text, $matches)) {
            $fields['issuing_authority'] = trim($matches[1]);
        }
        
        // Certificate holder name
        if (preg_match('/HOLDER[:\s]+NAME[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['holder_name'] = trim($matches[1]);
        } elseif (preg_match('/NAME[:\s]+OF[:\s]+HOLDER[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['holder_name'] = trim($matches[1]);
        }
        
        return $fields;
    }

    /**
     * Parse ID/Visa document
     */
    protected function parseIdVisa(string $text): array
    {
        $fields = [];
        
        // Document number
        if (preg_match('/DOCUMENT[:\s]+(?:NO|NUMBER|#)[:\s]*([A-Z0-9\-]+)/i', $text, $matches)) {
            $fields['document_number'] = trim($matches[1]);
        } elseif (preg_match('/VISA[:\s]+(?:NO|NUMBER|#)[:\s]*([A-Z0-9\-]+)/i', $text, $matches)) {
            $fields['document_number'] = trim($matches[1]);
        }
        
        // Visa type
        if (preg_match('/VISA[:\s]+TYPE[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['visa_type'] = trim($matches[1]);
        }
        
        // Issue date
        if (preg_match('/ISSUE[D]?[:\s]+(?:DATE|ON)[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['issue_date'] = $this->normalizeDate($matches[1]);
        }
        
        // Expiry date
        if (preg_match('/EXPIR[YIES]+[:\s]+(?:DATE|ON)[:\s]+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/i', $text, $matches)) {
            $fields['expiry_date'] = $this->normalizeDate($matches[1]);
        }
        
        // Issuing country
        if (preg_match('/ISSUED[:\s]+BY[:\s]+([A-Z\s]+)/i', $text, $matches)) {
            $fields['issuing_country'] = trim($matches[1]);
        }
        
        return $fields;
    }

    /**
     * Parse generic document
     */
    protected function parseGeneric(string $text): array
    {
        $fields = [];
        
        // Try to extract dates
        if (preg_match_all('/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})/', $text, $matches)) {
            $fields['dates_found'] = array_unique($matches[1]);
        }
        
        // Try to extract document numbers
        if (preg_match('/\b([A-Z0-9]{6,20})\b/', $text, $matches)) {
            $fields['document_number'] = $matches[1];
        }
        
        // Try to extract names
        if (preg_match('/NAME[:\s]+([A-Z\s]{3,50})/i', $text, $matches)) {
            $fields['name'] = trim($matches[1]);
        }
        
        return $fields;
    }

    /**
     * Normalize date format
     */
    protected function normalizeDate(string $dateStr): ?string
    {
        try {
            // Try various formats
            $formats = ['d/m/Y', 'm/d/Y', 'd-m-Y', 'Y-m-d', 'd M Y', 'd/m/y', 'm/d/y'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateStr);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Fallback: try Carbon
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Date normalization failed', [
                'date_str' => $dateStr,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Calculate overall confidence from extracted fields
     */
    protected function calculateOverallConfidence(array $fields): float
    {
        if (empty($fields)) {
            return 0;
        }
        
        // Simple calculation: more fields = higher confidence
        $fieldCount = count($fields);
        $filledFields = count(array_filter($fields, fn($v) => !empty($v) && $v !== null));
        
        return round(($filledFields / max($fieldCount, 1)) * 100, 2);
    }

    /**
     * Extract text from PDF using TesseractOCR (fallback for PDFs)
     */
    protected function extractTextFromPdf(string $filePath): array
    {
        try {
            $fullPath = Storage::disk('public')->path($filePath);
            
            if (!file_exists($fullPath)) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'PDF file not found'
                ];
            }

            // Check if Imagick is available for PDF conversion
            if (!extension_loaded('imagick') || !class_exists('Imagick')) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'Imagick extension not available for PDF processing. Please install php-imagick.'
                ];
            }

            $text = '';
            $imagick = new \Imagick();
            $imagick->setResolution(300, 300);
            $imagick->readImage($fullPath);
            
            // Process each page
            foreach ($imagick as $i => $page) {
                $page->setImageFormat('png');
                $tmpImage = storage_path("app/temp/page_{$i}_" . time() . ".png");
                
                // Ensure temp directory exists
                if (!is_dir(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }
                
                $page->writeImage($tmpImage);
                
                // OCR per page using TesseractOCR
                try {
                    $ocr = $this->createTesseractOCR($tmpImage);
                    $ocr->lang('eng')->psm(3)->oem(1);
                    $pageText = $ocr->run();
                    $text .= $pageText . "\n";
                } catch (\Exception $e) {
                    Log::warning("TesseractOCR failed for PDF page {$i}: " . $e->getMessage());
                }
                
                // Clean up temp file
                if (file_exists($tmpImage)) {
                    unlink($tmpImage);
                }
            }
            
            $imagick->clear();
            $imagick->destroy();
            
            if (empty(trim($text))) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'No text detected in PDF'
                ];
            }
            
            // Use default confidence for TesseractOCR (typically 70-80%)
            return [
                'success' => true,
                'text' => $text,
                'confidence' => 75.0, // Default confidence for TesseractOCR
                'annotations' => null
            ];
            
        } catch (\Exception $e) {
            Log::error('PDF OCR Error: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'text' => '',
                'confidence' => 0,
                'message' => 'PDF OCR processing failed: ' . $e->getMessage()
            ];
        }
    }
}
