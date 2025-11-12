<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItineraryRouteComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'stop_id',
        'user_id',
        'parent_id',
        'body',
        'attachments',
        'visibility',
        'status',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(ItineraryRoute::class, 'route_id');
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(ItineraryRouteStop::class, 'stop_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }
}

