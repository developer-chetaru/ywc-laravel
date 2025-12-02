<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contractor_id',
        'user_id',
        'title',
        'review',
        'service_type',
        'service_cost',
        'timeframe',
        'overall_rating',
        'quality_rating',
        'professionalism_rating',
        'pricing_rating',
        'timeliness_rating',
        'would_recommend',
        'would_hire_again',
        'is_anonymous',
        'is_verified',
        'service_date',
        'yacht_name',
        'helpful_count',
        'not_helpful_count',
        'is_approved',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'would_hire_again' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_flagged' => 'boolean',
        'service_date' => 'date',
        'service_cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (ContractorReview $review) {
            if ($review->is_approved) {
                $review->contractor->updateRatingStats();
            }
        });

        static::deleted(function (ContractorReview $review) {
            $review->contractor->updateRatingStats();
        });
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
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
