<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTaxAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'nationality',
        'current_residence',
        'days_in_countries',
        'permanent_address',
        'tax_residency_analysis',
        'tax_obligations',
        'optimization_opportunities',
        'notes',
    ];

    protected $casts = [
        'days_in_countries' => 'array',
        'tax_residency_analysis' => 'array',
        'tax_obligations' => 'array',
        'optimization_opportunities' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

