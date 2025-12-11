<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialEducationalContent extends Model
{
    protected $table = 'financial_educational_content';
    
    protected $fillable = [
        'title',
        'type',
        'description',
        'content',
        'difficulty',
        'duration_minutes',
        'modules',
        'file_path',
        'is_published',
        'order',
    ];

    protected $casts = [
        'modules' => 'array',
        'is_published' => 'boolean',
    ];
}

