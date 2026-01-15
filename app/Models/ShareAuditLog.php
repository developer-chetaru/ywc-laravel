<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shareable_type',
        'shareable_id',
        'share_type',
        'action',
        'ip_address',
        'user_agent',
        'country',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the parent shareable model (DocumentShare or ProfileShare).
     */
    public function shareable()
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeForShare($query, $shareType, $shareId)
    {
        return $query->where('share_type', $shareType)
            ->where('shareable_id', $shareId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
