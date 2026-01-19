<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'description',
        'badge_icon',
        'badge_color',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all document verifications for this level
     */
    public function documentVerifications()
    {
        return $this->hasMany(DocumentVerification::class);
    }

    /**
     * Scope to get active levels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by level (ascending)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level', 'asc');
    }
}
