<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ItineraryRoute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'template_source_id',
        'title',
        'description',
        'slug',
        'cover_image',
        'region',
        'difficulty',
        'season',
        'duration_days',
        'start_date',
        'end_date',
        'distance_nm',
        'average_leg_nm',
        'visibility',
        'status',
        'is_template',
        'is_featured',
        'rating_avg',
        'rating_count',
        'views_count',
        'copies_count',
        'favorites_count',
        'tags',
        'metadata',
        'analytics_snapshot',
        'published_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'distance_nm' => 'decimal:2',
        'average_leg_nm' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'tags' => 'array',
        'metadata' => 'array',
        'analytics_snapshot' => 'array',
        'published_at' => 'datetime',
        'is_template' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ItineraryRoute $route) {
            if (empty($route->slug)) {
                $route->slug = static::generateSlug($route->title ?? 'Untitled Route');
            }
        });

        static::created(function (ItineraryRoute $route) {
            // Use newInstance to ensure model defaults are applied
            $statistic = new \App\Models\ItineraryRouteStatistic([
                'route_id' => $route->id,
                'views_total' => 0,
                'views_unique' => 0,
                'copies_total' => 0,
                'reviews_count' => 0,
                'rating_avg' => 0.00,
                'favorites_count' => 0,
                'shares_count' => 0,
            ]);
            $statistic->save();
        });

        static::updating(function (ItineraryRoute $route) {
            if ($route->isDirty('title') && !$route->is_template) {
                $route->slug = static::generateSlug($route->title ?? 'Untitled Route');
            }
        });
    }

    public static function generateSlug(?string $title = null): string
    {
        $title = $title ?? 'Untitled Route';
        $base = Str::slug($title);
        if ($base === '') {
            $base = Str::random(8);
        }

        $slug = $base;
        $counter = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function templateSource(): BelongsTo
    {
        return $this->belongsTo(self::class, 'template_source_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(ItineraryRouteStop::class, 'route_id')->orderBy('sequence');
    }

    public function legs(): HasMany
    {
        return $this->hasMany(ItineraryRouteLeg::class, 'route_id')->orderBy('sequence');
    }

    public function crew(): HasMany
    {
        return $this->hasMany(ItineraryRouteCrew::class, 'route_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ItineraryRouteReview::class, 'route_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ItineraryRouteComment::class, 'route_id')->whereNull('parent_id');
    }

    public function weatherSnapshots(): HasMany
    {
        return $this->hasManyThrough(
            ItineraryWeatherSnapshot::class,
            ItineraryRouteStop::class,
            'route_id',
            'stop_id'
        );
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(ItineraryRouteStatistic::class, 'route_id');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public')->where('status', '!=', 'archived');
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function visibleTo(?User $user): bool
    {
        if ($this->visibility === 'public') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($this->isOwnedBy($user)) {
            return true;
        }

        if ($this->visibility === 'crew') {
            return $this->crew()
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere('email', $user->email);
                })
                ->where('status', 'accepted')
                ->exists();
        }

        return false;
    }
}

