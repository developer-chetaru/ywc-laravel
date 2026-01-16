<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentStatusChange extends Model
{
    protected $fillable = [
        'document_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who changed the status
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
