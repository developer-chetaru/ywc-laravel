<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItineraryRouteLeg extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'from_stop_id',
        'to_stop_id',
        'sequence',
        'distance_nm',
        'estimated_hours',
        'average_speed_knots',
        'sailing_notes',
        'weather_window',
        'metrics',
    ];

    protected $casts = [
        'distance_nm' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'average_speed_knots' => 'decimal:2',
        'sailing_notes' => 'array',
        'weather_window' => 'array',
        'metrics' => 'array',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(ItineraryRoute::class, 'route_id');
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(ItineraryRouteStop::class, 'from_stop_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(ItineraryRouteStop::class, 'to_stop_id');
    }
}

