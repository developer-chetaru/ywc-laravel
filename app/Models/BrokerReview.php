<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrokerReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'broker_id',
        'user_id',
        'title',
        'review',
        'overall_rating',
        'job_quality_rating',
        'communication_rating',
        'professionalism_rating',
        'fees_transparency_rating',
        'support_rating',
        'would_use_again',
        'would_recommend',
        'is_anonymous',
        'is_verified',
        'placement_date',
        'position_placed',
        'yacht_name',
        'placement_timeframe',
        'helpful_count',
        'not_helpful_count',
        'is_approved',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'would_use_again' => 'boolean',
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_flagged' => 'boolean',
        'placement_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (BrokerReview $review) {
            if ($review->is_approved) {
                $review->broker->updateRatingStats();
            }
        });

        static::deleted(function (BrokerReview $review) {
            $review->broker->updateRatingStats();
        });
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
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
}
