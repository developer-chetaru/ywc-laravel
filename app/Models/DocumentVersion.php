<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_id',
        'version_number',
        'file_path',
        'file_type',
        'file_size',
        'thumbnail_path',
        'metadata',
        'change_notes',
        'created_by',
        'ocr_data',
        'ocr_status',
        'ocr_confidence',
    ];

    protected $casts = [
        'metadata' => 'array',
        'ocr_data' => 'array',
        'ocr_confidence' => 'float',
        'file_size' => 'integer',
        'version_number' => 'integer',
    ];

    /**
     * Get the document this version belongs to
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who created this version
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        if ($this->file_size < 1024) {
            return $this->file_size . ' KB';
        }

        return number_format($this->file_size / 1024, 2) . ' MB';
    }

    /**
     * Scope to get versions ordered by version number (descending)
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('version_number', 'desc');
    }

    /**
     * Scope to get versions ordered by version number (ascending)
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('version_number', 'asc');
    }
}
