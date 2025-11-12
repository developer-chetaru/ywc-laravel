<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItineraryRouteReview extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'user_id',
        'rating',
        'comment',
        'media',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'media' => 'array',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(ItineraryRoute::class, 'route_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

