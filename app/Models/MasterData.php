<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    protected $fillable = [
        'type',
        'code',
        'name',
        'description',
        'sort_order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get data by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get route visibility options
     */
    public static function getRouteVisibility()
    {
        return static::ofType('route_visibility')->get();
    }

    /**
     * Get route status options
     */
    public static function getRouteStatus()
    {
        return static::ofType('route_status')->get();
    }

    /**
     * Get marina types
     */
    public static function getMarinaTypes()
    {
        return static::ofType('marina_type')->get();
    }

    /**
     * Get yacht types
     */
    public static function getYachtTypes()
    {
        return static::ofType('yacht_type')->get();
    }

    /**
     * Get countries
     */
    public static function getCountries()
    {
        return static::ofType('country')->get();
    }
}
