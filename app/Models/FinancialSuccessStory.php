<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSuccessStory extends Model
{
    protected $table = 'financial_success_stories';
    
    protected $fillable = [
        'name',
        'position',
        'age',
        'strategy_type',
        'story',
        'starting_point',
        'current_status',
        'advice',
        'photo_path',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'starting_point' => 'decimal:2',
        'current_status' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];
}

