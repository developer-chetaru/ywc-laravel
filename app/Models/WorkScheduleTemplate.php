<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkScheduleTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'yacht_id',
        'created_by',
        'name',
        'description',
        'category',
        'schedule_pattern',
        'default_start_time',
        'default_end_time',
        'default_break_minutes',
        'default_location_status',
        'default_work_type',
        'default_department',
        'is_active',
        'is_public',
        'usage_count',
        'metadata',
    ];

    protected $casts = [
        'schedule_pattern' => 'array',
        'default_start_time' => 'datetime',
        'default_end_time' => 'datetime',
        'default_break_minutes' => 'integer',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'usage_count' => 'integer',
        'metadata' => 'array',
    ];

    public function yacht(): BelongsTo
    {
        return $this->belongsTo(Yacht::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'charter_week' => 'Charter Week',
            'passage' => 'Passage',
            'shipyard' => 'Shipyard',
            'port_rotation' => 'Port Rotation',
            'watch_schedule' => 'Watch Schedule',
            'custom' => 'Custom',
            default => $this->category,
        };
    }
}

