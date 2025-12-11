<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAdvisor extends Model
{
    protected $fillable = [
        'name',
        'bio',
        'qualifications',
        'specializations',
        'languages',
        'email',
        'phone',
        'hourly_rate',
        'availability',
        'rating',
        'total_consultations',
        'is_active',
    ];

    protected $casts = [
        'qualifications' => 'array',
        'specializations' => 'array',
        'languages' => 'array',
        'availability' => 'array',
        'rating' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function consultations(): HasMany
    {
        return $this->hasMany(FinancialConsultation::class, 'advisor_id');
    }
}

