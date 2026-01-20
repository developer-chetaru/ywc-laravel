<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShareTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'can_download',
        'can_print',
        'can_share',
        'can_comment',
        'is_one_time',
        'max_views',
        'require_password',
        'require_watermark',
        'duration_days',
        'has_access_window',
        'is_default',
        'usage_count',
    ];

    protected $casts = [
        'can_download' => 'boolean',
        'can_print' => 'boolean',
        'can_share' => 'boolean',
        'can_comment' => 'boolean',
        'is_one_time' => 'boolean',
        'require_password' => 'boolean',
        'require_watermark' => 'boolean',
        'has_access_window' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Relationship: Template owner
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter templates for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get default templates
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get permissions summary
     */
    public function getPermissionsSummaryAttribute(): string
    {
        $permissions = [];
        
        if ($this->can_download) $permissions[] = 'Download';
        if ($this->can_print) $permissions[] = 'Print';
        if ($this->can_share) $permissions[] = 'Share';
        if ($this->can_comment) $permissions[] = 'Comment';
        
        return implode(', ', $permissions) ?: 'View Only';
    }

    /**
     * Get restrictions summary
     */
    public function getRestrictionsSummaryAttribute(): string
    {
        $restrictions = [];
        
        if ($this->is_one_time) $restrictions[] = 'One-time';
        if ($this->max_views) $restrictions[] = "{$this->max_views} views max";
        if ($this->require_password) $restrictions[] = 'Password';
        if ($this->require_watermark) $restrictions[] = 'Watermark';
        if ($this->duration_days) $restrictions[] = "{$this->duration_days} days";
        
        return implode(', ', $restrictions) ?: 'No restrictions';
    }
}
