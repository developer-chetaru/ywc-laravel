<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'document_criteria',
        'permissions',
        'expiry_duration_days',
        'default_message',
        'is_default',
    ];

    protected $casts = [
        'document_criteria' => 'array',
        'permissions' => 'array',
        'expiry_duration_days' => 'integer',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user who owns this template
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get user's templates
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get default templates
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
