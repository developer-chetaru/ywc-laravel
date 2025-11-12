<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'day_count',
        'itinerary_days',
        'user_id',
        'status',
    ];

    // Important: cast itinerary_days to array so JSON is returned as JS array
    protected $casts = [
        'itinerary_days' => 'array',
    ];
}
