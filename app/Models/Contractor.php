<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contractor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'business_name',
        'category',
        'description',
        'location',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'specialties',
        'languages',
        'emergency_service',
        'response_time',
        'service_area',
        'price_range',
        'logo',
        'rating_avg',
        'rating_count',
        'reviews_count',
        'quality_rating_avg',
        'professionalism_rating_avg',
        'pricing_rating_avg',
        'timeliness_rating_avg',
        'recommendation_count',
        'is_verified',
    ];

    protected $casts = [
        'emergency_service' => 'boolean',
        'is_verified' => 'boolean',
        'rating_avg' => 'decimal:2',
        'quality_rating_avg' => 'decimal:2',
        'professionalism_rating_avg' => 'decimal:2',
        'pricing_rating_avg' => 'decimal:2',
        'timeliness_rating_avg' => 'decimal:2',
        'specialties' => 'array',
        'languages' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Contractor $contractor) {
            if (empty($contractor->slug)) {
                $contractor->slug = Str::slug($contractor->name);
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(ContractorReview::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(ContractorReview::class);
    }

    public function gallery()
    {
        return $this->hasMany(ContractorGallery::class)->orderBy('order')->orderBy('id');
    }

    public function updateRatingStats()
    {
        $approvedReviews = $this->allReviews()->where('is_approved', true)->get();
        
        if ($approvedReviews->count() > 0) {
            $this->rating_avg = $approvedReviews->avg('overall_rating');
            $this->rating_count = $approvedReviews->count();
            $this->reviews_count = $approvedReviews->count();
            
            $this->quality_rating_avg = $approvedReviews->whereNotNull('quality_rating')->avg('quality_rating');
            $this->professionalism_rating_avg = $approvedReviews->whereNotNull('professionalism_rating')->avg('professionalism_rating');
            $this->pricing_rating_avg = $approvedReviews->whereNotNull('pricing_rating')->avg('pricing_rating');
            $this->timeliness_rating_avg = $approvedReviews->whereNotNull('timeliness_rating')->avg('timeliness_rating');
            
            $this->recommendation_count = $approvedReviews->where('would_recommend', true)->count();
        } else {
            $this->rating_avg = 0;
            $this->rating_count = 0;
            $this->reviews_count = 0;
            $this->quality_rating_avg = null;
            $this->professionalism_rating_avg = null;
            $this->pricing_rating_avg = null;
            $this->timeliness_rating_avg = null;
            $this->recommendation_count = 0;
        }
        
        $this->save();
    }
}
