<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'company_overview',
        'years_in_operation',
        'accreditations',
        'certifications',
        'training_facilities',
        'instructor_qualifications',
        'website',
        'email',
        'phone',
        'social_media_links',
        'rating_avg',
        'total_reviews',
        'pass_rate',
        'total_students_trained',
        'total_students_ywc',
        'response_time_hours',
        'cancellation_rate',
        'is_verified_partner',
        'is_active',
    ];

    protected $casts = [
        'accreditations' => 'array',
        'certifications' => 'array',
        'instructor_qualifications' => 'array',
        'social_media_links' => 'array',
        'rating_avg' => 'decimal:2',
        'pass_rate' => 'decimal:2',
        'cancellation_rate' => 'decimal:2',
        'is_verified_partner' => 'boolean',
        'is_active' => 'boolean',
        'years_in_operation' => 'integer',
        'total_reviews' => 'integer',
        'total_students_trained' => 'integer',
        'total_students_ywc' => 'integer',
        'response_time_hours' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($provider) {
            if (empty($provider->slug)) {
                $provider->slug = Str::slug($provider->name);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->hasMany(TrainingProviderCourse::class, 'provider_id');
    }

    public function activeCourses()
    {
        return $this->hasMany(TrainingProviderCourse::class, 'provider_id')
            ->where('is_active', true);
    }

    public function galleries()
    {
        return $this->hasMany(TrainingProviderGallery::class, 'provider_id');
    }

    public function bundles()
    {
        return $this->hasMany(TrainingCourseBundle::class, 'provider_id');
    }

    public function responses()
    {
        return $this->hasMany(TrainingProviderResponse::class, 'provider_id');
    }
}
