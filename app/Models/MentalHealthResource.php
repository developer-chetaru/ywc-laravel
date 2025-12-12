<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentalHealthResource extends Model
{
    use SoftDeletes;

    protected $table = 'mental_health_resources';

    protected $fillable = [
        'title',
        'description',
        'category',
        'resource_type',
        'content',
        'file_path',
        'thumbnail_path',
        'tags',
        'target_audience',
        'reading_time_minutes',
        'difficulty_level',
        'related_resources',
        'author',
        'publication_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'target_audience' => 'array',
        'related_resources' => 'array',
        'publication_date' => 'date',
        'average_rating' => 'decimal:2',
    ];
}
