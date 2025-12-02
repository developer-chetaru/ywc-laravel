<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Yacht extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'length_meters',
        'length_feet',
        'year_built',
        'flag_registry',
        'home_port',
        'beam',
        'draft',
        'gross_tonnage',
        'builder',
        'hull_number',
        'imo_number',
        'crew_capacity',
        'guest_capacity',
        'cabin_configuration',
        'max_speed',
        'cruising_speed',
        'range_nm',
        'fuel_capacity_liters',
        'water_capacity_liters',
        'engine_details',
        'navigation_systems',
        'safety_equipment',
        'amenities',
        'special_features',
        'status',
        'home_region',
        'typical_cruising_grounds',
        'season_schedule',
        'cover_image',
        // Ownership & Management
        'owner_name',
        'ownership_type',
        'captain_name',
        'management_company',
        'is_charter_available',
        'charter_rate',
        // Enhanced crew information
        'current_crew_size',
        'crew_structure',
        'rotation_schedule',
        // Ratings
        'rating_avg',
        'rating_count',
        'reviews_count',
        'recommendation_percentage',
        'yacht_quality_rating_avg',
        'crew_culture_rating_avg',
        'management_rating_avg',
        'benefits_rating_avg',
    ];

    protected $casts = [
        'length_meters' => 'decimal:2',
        'length_feet' => 'decimal:2',
        'beam' => 'decimal:2',
        'draft' => 'decimal:2',
        'max_speed' => 'decimal:2',
        'cruising_speed' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'yacht_quality_rating_avg' => 'decimal:2',
        'crew_culture_rating_avg' => 'decimal:2',
        'management_rating_avg' => 'decimal:2',
        'benefits_rating_avg' => 'decimal:2',
        'is_charter_available' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Yacht $yacht) {
            if (empty($yacht->slug)) {
                $yacht->slug = Str::slug($yacht->name);
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(YachtReview::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(YachtReview::class);
    }

    public function managementResponses()
    {
        return $this->hasMany(YachtManagementResponse::class);
    }

    public function updateRatingStats()
    {
        $approvedReviews = $this->allReviews()->where('is_approved', true)->get();
        
        if ($approvedReviews->count() > 0) {
            $this->rating_avg = $approvedReviews->avg('overall_rating');
            $this->rating_count = $approvedReviews->count();
            $this->reviews_count = $approvedReviews->count();
            
            // Calculate category averages
            $this->yacht_quality_rating_avg = $approvedReviews->whereNotNull('yacht_quality_rating')->avg('yacht_quality_rating');
            $this->crew_culture_rating_avg = $approvedReviews->whereNotNull('crew_culture_rating')->avg('crew_culture_rating');
            $this->management_rating_avg = $approvedReviews->whereNotNull('management_rating')->avg('management_rating');
            $this->benefits_rating_avg = $approvedReviews->whereNotNull('benefits_rating')->avg('benefits_rating');
            
            $recommendCount = $approvedReviews->where('would_recommend', true)->count();
            $this->recommendation_percentage = round(($recommendCount / $approvedReviews->count()) * 100);
        } else {
            $this->rating_avg = 0;
            $this->rating_count = 0;
            $this->reviews_count = 0;
            $this->recommendation_percentage = 0;
            $this->yacht_quality_rating_avg = null;
            $this->crew_culture_rating_avg = null;
            $this->management_rating_avg = null;
            $this->benefits_rating_avg = null;
        }
        
        $this->save();
    }
}

