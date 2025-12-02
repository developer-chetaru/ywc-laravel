<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Broker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'business_name',
        'type',
        'description',
        'primary_location',
        'office_locations',
        'phone',
        'email',
        'website',
        'specialties',
        'fee_structure',
        'regions_served',
        'years_in_business',
        'is_myba_member',
        'is_licensed',
        'is_verified',
        'certifications',
        'logo',
        'rating_avg',
        'rating_count',
        'reviews_count',
        'job_quality_rating_avg',
        'communication_rating_avg',
        'professionalism_rating_avg',
        'fees_transparency_rating_avg',
        'support_rating_avg',
        'would_use_again_count',
        'would_recommend_count',
        'average_placement_time',
        'positions_per_month',
        'success_rate',
    ];

    protected $casts = [
        'is_myba_member' => 'boolean',
        'is_licensed' => 'boolean',
        'is_verified' => 'boolean',
        'rating_avg' => 'decimal:2',
        'job_quality_rating_avg' => 'decimal:2',
        'communication_rating_avg' => 'decimal:2',
        'professionalism_rating_avg' => 'decimal:2',
        'fees_transparency_rating_avg' => 'decimal:2',
        'support_rating_avg' => 'decimal:2',
        'success_rate' => 'decimal:2',
        'office_locations' => 'array',
        'specialties' => 'array',
        'regions_served' => 'array',
        'certifications' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Broker $broker) {
            if (empty($broker->slug)) {
                $broker->slug = Str::slug($broker->name);
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(BrokerReview::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(BrokerReview::class);
    }

    public function updateRatingStats()
    {
        $approvedReviews = $this->allReviews()->where('is_approved', true)->get();
        
        if ($approvedReviews->count() > 0) {
            $this->rating_avg = $approvedReviews->avg('overall_rating');
            $this->rating_count = $approvedReviews->count();
            $this->reviews_count = $approvedReviews->count();
            
            $this->job_quality_rating_avg = $approvedReviews->whereNotNull('job_quality_rating')->avg('job_quality_rating');
            $this->communication_rating_avg = $approvedReviews->whereNotNull('communication_rating')->avg('communication_rating');
            $this->professionalism_rating_avg = $approvedReviews->whereNotNull('professionalism_rating')->avg('professionalism_rating');
            $this->fees_transparency_rating_avg = $approvedReviews->whereNotNull('fees_transparency_rating')->avg('fees_transparency_rating');
            $this->support_rating_avg = $approvedReviews->whereNotNull('support_rating')->avg('support_rating');
            
            $this->would_use_again_count = $approvedReviews->where('would_use_again', true)->count();
            $this->would_recommend_count = $approvedReviews->where('would_recommend', true)->count();
        } else {
            $this->rating_avg = 0;
            $this->rating_count = 0;
            $this->reviews_count = 0;
            $this->job_quality_rating_avg = null;
            $this->communication_rating_avg = null;
            $this->professionalism_rating_avg = null;
            $this->fees_transparency_rating_avg = null;
            $this->support_rating_avg = null;
            $this->would_use_again_count = 0;
            $this->would_recommend_count = 0;
        }
        
        $this->save();
    }
}
