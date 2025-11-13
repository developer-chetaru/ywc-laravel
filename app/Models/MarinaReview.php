<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarinaReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'marina_id',
        'user_id',
        'title',
        'review',
        'tips_tricks',
        'overall_rating',
        'fuel_rating',
        'water_rating',
        'electricity_rating',
        'wifi_rating',
        'showers_rating',
        'laundry_rating',
        'maintenance_rating',
        'provisioning_rating',
        'staff_rating',
        'value_rating',
        'protection_rating',
        'is_anonymous',
        'is_verified',
        'visit_date',
        'yacht_length_meters',
        'helpful_count',
        'not_helpful_count',
        'is_approved',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'visit_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (MarinaReview $review) {
            if ($review->is_approved) {
                $review->marina->updateRatingStats();
            }
        });

        static::deleted(function (MarinaReview $review) {
            $review->marina->updateRatingStats();
        });
    }

    public function marina()
    {
        return $this->belongsTo(Marina::class);
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

    public function userVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}

