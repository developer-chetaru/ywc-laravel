<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrewdentialsSync extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'sync_type',
        'direction',
        'status',
        'crewdentials_document_id',
        'crewdentials_response',
        'error_message',
        'retry_count',
        'last_retry_at',
        'synced_at',
    ];

    protected $casts = [
        'crewdentials_response' => 'array',
        'last_retry_at' => 'datetime',
        'synced_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Scope for failed syncs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', 3);
    }

    /**
     * Scope for pending syncs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
