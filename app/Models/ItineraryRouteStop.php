<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItineraryRouteStop extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'sequence',
        'day_number',
        'name',
        'location_label',
        'latitude',
        'longitude',
        'stay_duration_hours',
        'description',
        'notes',
        'photos',
        'tasks',
        'checklists',
        'eta',
        'ata',
        'departure_actual',
        'metadata',
        'requires_clearance',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'stay_duration_hours' => 'integer',
        'photos' => 'array',
        'tasks' => 'array',
        'checklists' => 'array',
        'eta' => 'datetime',
        'ata' => 'datetime',
        'departure_actual' => 'datetime',
        'metadata' => 'array',
        'requires_clearance' => 'boolean',
    ];

    protected $touches = [
        'route',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(ItineraryRoute::class, 'route_id');
    }

    public function incomingLegs(): HasMany
    {
        return $this->hasMany(ItineraryRouteLeg::class, 'to_stop_id');
    }

    public function outgoingLegs(): HasMany
    {
        return $this->hasMany(ItineraryRouteLeg::class, 'from_stop_id');
    }

    public function weatherSnapshots(): HasMany
    {
        return $this->hasMany(ItineraryWeatherSnapshot::class, 'stop_id')->latest('forecast_date');
    }
}

