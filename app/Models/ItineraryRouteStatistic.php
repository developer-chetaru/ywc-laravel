<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryRouteStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'views_total',
        'views_unique',
        'copies_total',
        'reviews_count',
        'rating_avg',
        'favorites_count',
        'shares_count',
        'last_viewed_at',
        'regions_breakdown',
        'analytics',
    ];

    protected $casts = [
        'rating_avg' => 'decimal:2',
        'regions_breakdown' => 'array',
        'analytics' => 'array',
        'last_viewed_at' => 'datetime',
    ];

    protected $attributes = [
        'views_total' => 0,
        'views_unique' => 0,
        'copies_total' => 0,
        'reviews_count' => 0,
        'rating_avg' => 0,
        'favorites_count' => 0,
        'shares_count' => 0,
    ];

    protected static function booted(): void
    {
        static::creating(function (ItineraryRouteStatistic $statistic) {
            // Ensure all required fields have default values - use strict type checking
            if (!isset($statistic->views_total) || $statistic->views_total === null) {
                $statistic->views_total = 0;
            }
            if (!isset($statistic->views_unique) || $statistic->views_unique === null) {
                $statistic->views_unique = 0;
            }
            if (!isset($statistic->copies_total) || $statistic->copies_total === null) {
                $statistic->copies_total = 0;
            }
            if (!isset($statistic->reviews_count) || $statistic->reviews_count === null) {
                $statistic->reviews_count = 0;
            }
            if (!isset($statistic->rating_avg) || $statistic->rating_avg === null) {
                $statistic->rating_avg = 0.00;
            }
            if (!isset($statistic->favorites_count) || $statistic->favorites_count === null) {
                $statistic->favorites_count = 0;
            }
            if (!isset($statistic->shares_count) || $statistic->shares_count === null) {
                $statistic->shares_count = 0;
            }
        });

        static::updating(function (ItineraryRouteStatistic $statistic) {
            // Ensure null values are converted to defaults during updates
            if ($statistic->isDirty('reviews_count') && $statistic->reviews_count === null) {
                $statistic->reviews_count = 0;
            }
            if ($statistic->isDirty('rating_avg') && $statistic->rating_avg === null) {
                $statistic->rating_avg = 0.00;
            }
            if ($statistic->isDirty('views_total') && $statistic->views_total === null) {
                $statistic->views_total = 0;
            }
            if ($statistic->isDirty('views_unique') && $statistic->views_unique === null) {
                $statistic->views_unique = 0;
            }
            if ($statistic->isDirty('copies_total') && $statistic->copies_total === null) {
                $statistic->copies_total = 0;
            }
            if ($statistic->isDirty('favorites_count') && $statistic->favorites_count === null) {
                $statistic->favorites_count = 0;
            }
            if ($statistic->isDirty('shares_count') && $statistic->shares_count === null) {
                $statistic->shares_count = 0;
            }
        });
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(ItineraryRoute::class, 'route_id');
    }
}

