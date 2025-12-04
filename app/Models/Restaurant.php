<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'cuisine_type',
        'price_range',
        'opening_hours',
        'crew_friendly',
        'crew_discount',
        'crew_discount_details',
        'cover_image',
        'rating_avg',
        'rating_count',
        'reviews_count',
        'recommendation_count',
        'is_verified',
    ];

    protected $casts = [
        'crew_friendly' => 'boolean',
        'crew_discount' => 'boolean',
        'is_verified' => 'boolean',
        'rating_avg' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'opening_hours' => 'array',
        'cuisine_type' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Restaurant $restaurant) {
            if (empty($restaurant->slug)) {
                $restaurant->slug = Str::slug($restaurant->name);
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(RestaurantReview::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(RestaurantReview::class);
    }

    public function gallery()
    {
        return $this->hasMany(RestaurantGallery::class)->orderBy('order')->orderBy('id');
    }

    public function updateRatingStats()
    {
        $approvedReviews = $this->allReviews()->where('is_approved', true)->get();
        
        if ($approvedReviews->count() > 0) {
            $this->rating_avg = $approvedReviews->avg('overall_rating');
            $this->rating_count = $approvedReviews->count();
            $this->reviews_count = $approvedReviews->count();
            $this->recommendation_count = $approvedReviews->where('would_recommend', true)->count();
        } else {
            $this->rating_avg = 0;
            $this->rating_count = 0;
            $this->reviews_count = 0;
            $this->recommendation_count = 0;
        }
        
        $this->save();
    }
}
