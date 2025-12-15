<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_course_id',
        'schedule_id',
        'rating_overall',
        'rating_content',
        'rating_instructor',
        'rating_facilities',
        'rating_value',
        'rating_administration',
        'would_recommend',
        'review_text',
        'liked_most',
        'areas_for_improvement',
        'tips_for_students',
        'date_attended',
        'is_verified_student',
        'is_approved',
    ];

    protected $casts = [
        'rating_overall' => 'integer',
        'rating_content' => 'integer',
        'rating_instructor' => 'integer',
        'rating_facilities' => 'integer',
        'rating_value' => 'integer',
        'rating_administration' => 'integer',
        'would_recommend' => 'boolean',
        'is_verified_student' => 'boolean',
        'is_approved' => 'boolean',
        'date_attended' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function ($review) {
            // Update course rating averages
            $course = $review->providerCourse;
            if ($course) {
                $approvedReviews = $course->reviews()->where('is_approved', true);
                $course->rating_avg = $approvedReviews->avg('rating_overall') ?? 0;
                $course->review_count = $approvedReviews->count();
                $course->save();

                // Update provider rating averages
                $provider = $course->provider;
                if ($provider) {
                    $providerReviews = TrainingCourseReview::whereHas('providerCourse', function ($query) use ($provider) {
                        $query->where('provider_id', $provider->id);
                    })->where('is_approved', true);
                    $provider->rating_avg = $providerReviews->avg('rating_overall') ?? 0;
                    $provider->total_reviews = $providerReviews->count();
                    $provider->save();
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function providerCourse()
    {
        return $this->belongsTo(TrainingProviderCourse::class, 'provider_course_id');
    }

    public function schedule()
    {
        return $this->belongsTo(TrainingCourseSchedule::class, 'schedule_id');
    }

    public function providerResponse()
    {
        return $this->hasOne(TrainingProviderResponse::class, 'review_id');
    }
}
