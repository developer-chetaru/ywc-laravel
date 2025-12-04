<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Marina extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'country',
        'region',
        'city',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'vhf_channel',
        'operating_hours',
        'emergency_contact',
        'type',
        'total_berths',
        'max_length_meters',
        'max_draft_meters',
        'max_beam_meters',
        'fuel_diesel',
        'fuel_gasoline',
        'fuel_info',
        'water_available',
        'water_info',
        'electricity_available',
        'electricity_info',
        'wifi_available',
        'wifi_info',
        'showers_available',
        'showers_info',
        'laundry_available',
        'laundry_info',
        'maintenance_available',
        'maintenance_info',
        'provisioning_available',
        'provisioning_info',
        'amenities',
        'marine_services',
        'safety_security',
        'pricing_info',
        'value_rating',
        'location_accessibility',
        'weather_protection',
        'mooring_info',
        'best_time_to_visit',
        'nearby_attractions',
        'customs_regulations',
        'cover_image',
        'rating_avg',
        'rating_count',
        'reviews_count',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'max_length_meters' => 'decimal:2',
        'max_draft_meters' => 'decimal:2',
        'max_beam_meters' => 'decimal:2',
        'fuel_diesel' => 'boolean',
        'fuel_gasoline' => 'boolean',
        'water_available' => 'boolean',
        'electricity_available' => 'boolean',
        'wifi_available' => 'boolean',
        'showers_available' => 'boolean',
        'laundry_available' => 'boolean',
        'maintenance_available' => 'boolean',
        'provisioning_available' => 'boolean',
        'amenities' => 'array',
        'marine_services' => 'array',
        'safety_security' => 'array',
        'rating_avg' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Marina $marina) {
            if (empty($marina->slug)) {
                $marina->slug = Str::slug($marina->name . ' ' . $marina->city);
            }
        });
    }

    public function reviews()
    {
        return $this->hasMany(MarinaReview::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(MarinaReview::class);
    }

    public function gallery()
    {
        return $this->hasMany(MarinaGallery::class)->orderBy('order')->orderBy('id');
    }

    public function updateRatingStats()
    {
        $approvedReviews = $this->allReviews()->where('is_approved', true)->get();
        
        if ($approvedReviews->count() > 0) {
            $this->rating_avg = $approvedReviews->avg('overall_rating');
            $this->rating_count = $approvedReviews->count();
            $this->reviews_count = $approvedReviews->count();
        } else {
            $this->rating_avg = 0;
            $this->rating_count = 0;
            $this->reviews_count = 0;
        }
        
        $this->save();
    }
}

