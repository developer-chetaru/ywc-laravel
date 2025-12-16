<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobRating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_post_id',
        'temporary_work_booking_id',
        'rater_user_id',
        'rated_user_id',
        'rated_yacht_id',
        'rating_type',
        'professionalism_rating',
        'payment_rating',
        'management_rating',
        'vessel_quality_rating',
        'program_accuracy_rating',
        'work_life_balance_rating',
        'crew_professionalism_rating',
        'crew_skills_quality_rating',
        'crew_reliability_rating',
        'crew_attitude_teamwork_rating',
        'crew_communication_rating',
        'overall_rating',
        'would_work_here_again',
        'would_hire_again',
        'review_text',
        'is_anonymous',
        'is_verified',
        'is_approved',
        'is_flagged',
        'flag_reason',
        'flagged_by_user_id',
        'vessel_response',
        'vessel_responded_at',
        'private_feedback',
        'added_to_preferred_list',
        'allow_feature_publicly',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_flagged' => 'boolean',
        'added_to_preferred_list' => 'boolean',
        'allow_feature_publicly' => 'boolean',
        'vessel_responded_at' => 'datetime',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function temporaryWorkBooking()
    {
        return $this->belongsTo(TemporaryWorkBooking::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_user_id');
    }

    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }

    public function ratedYacht()
    {
        return $this->belongsTo(Yacht::class, 'rated_yacht_id');
    }
}
