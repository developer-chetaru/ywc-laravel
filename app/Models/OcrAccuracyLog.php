<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcrAccuracyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'original_ocr_data',
        'corrected_data',
        'field_accuracy',
        'overall_accuracy',
        'fields_correct',
        'fields_total',
        'characters_correct',
        'characters_total',
        'ocr_engine',
        'ocr_version',
        'document_type',
        'document_language',
        'confidence_score',
        'manual_correction_required',
        'correction_notes',
        'corrected_at',
    ];

    protected $casts = [
        'original_ocr_data' => 'array',
        'corrected_data' => 'array',
        'field_accuracy' => 'array',
        'overall_accuracy' => 'decimal:2',
        'manual_correction_required' => 'boolean',
        'corrected_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate field-level accuracy
     */
    public static function calculateFieldAccuracy(array $original, array $corrected): array
    {
        $accuracy = [];

        foreach ($original as $field => $originalValue) {
            $correctedValue = $corrected[$field] ?? $originalValue;
            
            if ($originalValue === $correctedValue) {
                $accuracy[$field] = 100;
            } else {
                // Calculate Levenshtein distance for string similarity
                $similarity = 0;
                if (is_string($originalValue) && is_string($correctedValue)) {
                    similar_text($originalValue, $correctedValue, $similarity);
                }
                $accuracy[$field] = round($similarity, 2);
            }
        }

        return $accuracy;
    }

    /**
     * Calculate overall accuracy
     */
    public static function calculateOverallAccuracy(array $fieldAccuracy): float
    {
        if (empty($fieldAccuracy)) {
            return 0;
        }

        $total = array_sum($fieldAccuracy);
        $count = count($fieldAccuracy);

        return round($total / $count, 2);
    }

    /**
     * Log OCR correction
     */
    public static function logCorrection(int $documentId, array $original, array $corrected, ?int $userId = null): self
    {
        $fieldAccuracy = self::calculateFieldAccuracy($original, $corrected);
        $overallAccuracy = self::calculateOverallAccuracy($fieldAccuracy);

        $fieldsCorrect = count(array_filter($fieldAccuracy, fn($acc) => $acc == 100));
        $fieldsTotal = count($fieldAccuracy);

        // Calculate character-level accuracy
        $originalText = implode(' ', array_values($original));
        $correctedText = implode(' ', array_values($corrected));
        $charactersTotal = strlen($originalText);
        $charactersCorrect = 0;
        
        similar_text($originalText, $correctedText, $charSimilarity);
        $charactersCorrect = round(($charSimilarity / 100) * $charactersTotal);

        return self::create([
            'document_id' => $documentId,
            'user_id' => $userId,
            'original_ocr_data' => $original,
            'corrected_data' => $corrected,
            'field_accuracy' => $fieldAccuracy,
            'overall_accuracy' => $overallAccuracy,
            'fields_correct' => $fieldsCorrect,
            'fields_total' => $fieldsTotal,
            'characters_correct' => $charactersCorrect,
            'characters_total' => $charactersTotal,
            'manual_correction_required' => $overallAccuracy < 90,
            'corrected_at' => now(),
        ]);
    }

    /**
     * Get accuracy statistics
     */
    public static function getAccuracyStats(array $filters = []): array
    {
        $query = self::query();

        if (isset($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        if (isset($filters['ocr_engine'])) {
            $query->where('ocr_engine', $filters['ocr_engine']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $logs = $query->get();

        return [
            'total_corrections' => $logs->count(),
            'average_accuracy' => $logs->avg('overall_accuracy'),
            'min_accuracy' => $logs->min('overall_accuracy'),
            'max_accuracy' => $logs->max('overall_accuracy'),
            'total_fields_corrected' => $logs->sum('fields_total') - $logs->sum('fields_correct'),
            'accuracy_by_engine' => $logs->groupBy('ocr_engine')->map(fn($group) => $group->avg('overall_accuracy')),
            'accuracy_by_type' => $logs->groupBy('document_type')->map(fn($group) => $group->avg('overall_accuracy')),
        ];
    }
}
