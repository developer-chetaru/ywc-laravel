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
        'rating_avg',
        'rating_count',
        'reviews_count',
        'recommendation_percentage',
    ];

    protected $casts = [
        'length_meters' => 'decimal:2',
        'length_feet' => 'decimal:2',
        'beam' => 'decimal:2',
        'draft' => 'decimal:2',
        'max_speed' => 'decimal:2',
        'cruising_speed' => 'decimal:2',
        'rating_avg' => 'decimal:2',
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
            
            $recommendCount = $approvedReviews->where('would_recommend', true)->count();
            $this->recommendation_percentage = round(($recommendCount / $approvedReviews->count()) * 100);
        } else {
            $this->rating_avg = 0;
            $this->rating_count = 0;
            $this->reviews_count = 0;
            $this->recommendation_percentage = 0;
        }
        
        $this->save();
    }
}

