<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_course_id',
        'location_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'available_spots',
        'booked_spots',
        'is_full',
        'is_cancelled',
        'cancellation_reason',
        'early_bird_price',
        'early_bird_deadline',
        'last_minute_price',
        'group_booking_available',
        'group_min_size',
        'group_discount_percentage',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'early_bird_deadline' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'early_bird_price' => 'decimal:2',
        'last_minute_price' => 'decimal:2',
        'group_discount_percentage' => 'decimal:2',
        'is_full' => 'boolean',
        'is_cancelled' => 'boolean',
        'group_booking_available' => 'boolean',
        'available_spots' => 'integer',
        'booked_spots' => 'integer',
        'group_min_size' => 'integer',
    ];

    public function providerCourse()
    {
        return $this->belongsTo(TrainingProviderCourse::class, 'provider_course_id');
    }

    public function location()
    {
        return $this->belongsTo(TrainingCourseLocation::class, 'location_id');
    }

    public function reviews()
    {
        return $this->hasMany(TrainingCourseReview::class, 'schedule_id');
    }

    // Check if early bird pricing applies
    public function isEarlyBirdAvailable()
    {
        if (!$this->early_bird_price || !$this->early_bird_deadline) {
            return false;
        }
        return now()->lte($this->early_bird_deadline);
    }

    // Get current price (early bird, last minute, or regular)
    public function getCurrentPriceAttribute()
    {
        if ($this->isEarlyBirdAvailable()) {
            return $this->early_bird_price;
        }
        // Could add last minute logic here
        return $this->providerCourse->price;
    }
}
