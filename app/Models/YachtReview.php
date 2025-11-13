<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YachtReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'yacht_id',
        'user_id',
        'title',
        'review',
        'pros',
        'cons',
        'overall_rating',
        'management_rating',
        'working_conditions_rating',
        'compensation_rating',
        'crew_welfare_rating',
        'yacht_condition_rating',
        'career_development_rating',
        'would_recommend',
        'is_anonymous',
        'is_verified',
        'work_start_date',
        'work_end_date',
        'position_held',
        'helpful_count',
        'not_helpful_count',
        'is_approved',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'work_start_date' => 'date',
        'work_end_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (YachtReview $review) {
            if ($review->is_approved) {
                $review->yacht->updateRatingStats();
            }
        });

        static::deleted(function (YachtReview $review) {
            $review->yacht->updateRatingStats();
        });
    }

    public function yacht()
    {
        return $this->belongsTo(Yacht::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->morphMany(ReviewPhoto::class, 'reviewable')->where('review_id', $this->id);
    }

    public function votes()
    {
        return $this->morphMany(ReviewVote::class, 'reviewable')->where('review_id', $this->id);
    }

    public function comments()
    {
        return $this->morphMany(ReviewComment::class, 'reviewable')->where('review_id', $this->id)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->morphMany(ReviewComment::class, 'reviewable')->where('review_id', $this->id);
    }

    public function managementResponse()
    {
        return $this->hasOne(YachtManagementResponse::class);
    }

    public function userVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}

