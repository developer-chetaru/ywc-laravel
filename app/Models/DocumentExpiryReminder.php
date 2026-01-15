<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentExpiryReminder extends Model
{
    protected $fillable = [
        'document_id',
        'reminder_type',
        'sent_at',
        'expiry_date',
        'email_content',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    /**
     * Get the document this reminder is for
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Check if a reminder of this type was already sent for this document
     */
    public static function wasSentForDocument(int $documentId, string $reminderType): bool
    {
        return self::where('document_id', $documentId)
            ->where('reminder_type', $reminderType)
            ->exists();
    }
}
