<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialCalculation extends Model
{
    protected $fillable = [
        'user_id',
        'calculator_type',
        'input_data',
        'result_data',
        'session_id',
        'is_saved',
    ];

    protected $casts = [
        'input_data' => 'array',
        'result_data' => 'array',
        'is_saved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
