<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'title',
        'review',
        'overall_rating',
        'food_rating',
        'service_rating',
        'atmosphere_rating',
        'value_rating',
        'would_recommend',
        'is_anonymous',
        'is_verified',
        'visit_date',
        'crew_tips',
        'helpful_count',
        'not_helpful_count',
        'is_approved',
        'is_flagged',
        'flag_reason',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_flagged' => 'boolean',
        'visit_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (RestaurantReview $review) {
            if ($review->is_approved) {
                $review->restaurant->updateRatingStats();
            }
        });

        static::deleted(function (RestaurantReview $review) {
            $review->restaurant->updateRatingStats();
        });
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
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
