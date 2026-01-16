<?php

namespace App\Services\Documents;

use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type as FeatureType;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OcrService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_cloud.api_key');
        
        // Initialize client with API key
        if ($this->apiKey) {
            $this->client = new ImageAnnotatorClient([
                'key' => $this->apiKey,
            ]);
        } else {
            Log::warning('Google Cloud Vision API key not configured');
        }
    }

    /**
     * Extract text from document image/PDF
     */
    public function extractText(string $filePath): array
    {
        if (!$this->client) {
            return [
                'success' => false,
                'text' => '',
                'confidence' => 0,
                'message' => 'Google Cloud Vision API not configured'
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
            
            // Create Image object with content
            $image = new Image();
            $image->setContent($imageContent);
            
            // Create Feature for document text detection
            $feature = new Feature();
            $feature->setType(FeatureType::DOCUMENT_TEXT_DETECTION);
            
            // Create annotation request
            $request = new AnnotateImageRequest();
            $request->setImage($image);
            $request->setFeatures([$feature]);
            
            // Create batch request
            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$request]);
            
            // Call API
            $response = $this->client->batchAnnotateImages($batchRequest);
            $responses = $response->getResponses();
            
            if (empty($responses)) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'No response from Google Vision API'
                ];
            }
            
            $annotateResponse = $responses[0];
            
            // Check for errors
            if ($annotateResponse->hasError()) {
                $error = $annotateResponse->getError();
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'Google Vision API Error: ' . $error->getMessage()
                ];
            }
            
            // Get full text annotation
            if (!$annotateResponse->hasFullTextAnnotation()) {
                return [
                    'success' => false,
                    'text' => '',
                    'confidence' => 0,
                    'message' => 'No text detected in document'
                ];
            }
            
            $fullTextAnnotation = $annotateResponse->getFullTextAnnotation();
            $fullText = $fullTextAnnotation->getText();
            
            // Calculate confidence from text annotations
            // Use a default confidence if we can't calculate it precisely
            $avgConfidence = 85.0; // Default confidence for document text detection
            
            // Try to get confidence from pages/blocks if available
            try {
                $pages = $fullTextAnnotation->getPages();
                $confidences = [];
                foreach ($pages as $page) {
                    $blocks = $page->getBlocks();
                    foreach ($blocks as $block) {
                        $paragraphs = $block->getParagraphs();
                        foreach ($paragraphs as $paragraph) {
                            $words = $paragraph->getWords();
                            foreach ($words as $word) {
                                $symbols = $word->getSymbols();
                                foreach ($symbols as $symbol) {
                                    if (method_exists($symbol, 'getConfidence')) {
                                        $conf = $symbol->getConfidence();
                                        if ($conf > 0) {
                                            $confidences[] = $conf;
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
            } catch (\Exception $e) {
                // Use default confidence if calculation fails
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
     * Clean up client
     */
    public function __destruct()
    {
        if ($this->client) {
            $this->client->close();
        }
    }
}
