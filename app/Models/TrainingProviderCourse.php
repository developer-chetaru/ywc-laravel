<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProviderCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'certification_id',
        'provider_id',
        'price',
        'ywc_discount_percentage',
        'duration_days',
        'duration_hours',
        'class_size_max',
        'language_of_instruction',
        'format',
        'course_structure',
        'daily_schedule',
        'learning_outcomes',
        'assessment_methods',
        'materials_included',
        'accommodation_included',
        'accommodation_details',
        'meals_included',
        'meals_details',
        'parking_included',
        'transport_included',
        're_sits_included',
        'special_features',
        'booking_url',
        'ywc_tracking_code',
        'rating_avg',
        'review_count',
        'view_count',
        'click_through_count',
        'booking_count',
        'is_active',
        'requires_admin_approval',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'ywc_discount_percentage' => 'decimal:2',
        'daily_schedule' => 'array',
        'learning_outcomes' => 'array',
        'assessment_methods' => 'array',
        'materials_included' => 'array',
        'accommodation_included' => 'boolean',
        'meals_included' => 'boolean',
        'parking_included' => 'boolean',
        'transport_included' => 'boolean',
        're_sits_included' => 'boolean',
        'is_active' => 'boolean',
        'requires_admin_approval' => 'boolean',
        'rating_avg' => 'decimal:2',
        'duration_days' => 'integer',
        'duration_hours' => 'integer',
        'class_size_max' => 'integer',
        'review_count' => 'integer',
        'view_count' => 'integer',
        'click_through_count' => 'integer',
        'booking_count' => 'integer',
    ];

    public function certification()
    {
        return $this->belongsTo(TrainingCertification::class, 'certification_id');
    }

    public function provider()
    {
        return $this->belongsTo(TrainingProvider::class, 'provider_id');
    }

    public function locations()
    {
        return $this->hasMany(TrainingCourseLocation::class, 'provider_course_id');
    }

    public function schedules()
    {
        return $this->hasMany(TrainingCourseSchedule::class, 'provider_course_id');
    }

    public function upcomingSchedules()
    {
        return $this->hasMany(TrainingCourseSchedule::class, 'provider_course_id')
            ->where('start_date', '>=', now())
            ->where('is_cancelled', false)
            ->where('is_full', false)
            ->orderBy('start_date');
    }

    public function reviews()
    {
        return $this->hasMany(TrainingCourseReview::class, 'provider_course_id')
            ->where('is_approved', true);
    }

    public function userCertifications()
    {
        return $this->hasMany(TrainingUserCertification::class, 'provider_course_id');
    }

    // Calculate YWC member price
    public function getYwcPriceAttribute()
    {
        $discount = $this->ywc_discount_percentage / 100;
        return $this->price * (1 - $discount);
    }

    // Calculate savings amount
    public function getSavingsAmountAttribute()
    {
        return $this->price - $this->ywc_price;
    }
}
