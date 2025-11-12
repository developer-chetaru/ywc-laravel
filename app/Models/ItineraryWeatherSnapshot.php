<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryWeatherSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'stop_id',
        'forecast_date',
        'payload',
        'fetched_at',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'payload' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function stop(): BelongsTo
    {
        return $this->belongsTo(ItineraryRouteStop::class, 'stop_id');
    }
}

