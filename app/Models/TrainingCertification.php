<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainingCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'official_designation',
        'description',
        'prerequisites',
        'validity_period_months',
        'renewal_requirements',
        'international_recognition',
        'career_benefits',
        'positions_requiring',
        'related_certifications',
        'recommended_progression',
        'cover_image',
        'sample_certificates',
        'requires_admin_approval',
        'is_active',
        'provider_count',
    ];

    protected $casts = [
        'related_certifications' => 'array',
        'recommended_progression' => 'array',
        'sample_certificates' => 'array',
        'requires_admin_approval' => 'boolean',
        'is_active' => 'boolean',
        'validity_period_months' => 'integer',
        'provider_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($certification) {
            if (empty($certification->slug)) {
                $certification->slug = Str::slug($certification->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(TrainingCertificationCategory::class, 'category_id');
    }

    public function providerCourses()
    {
        return $this->hasMany(TrainingProviderCourse::class, 'certification_id');
    }

    public function activeProviderCourses()
    {
        return $this->hasMany(TrainingProviderCourse::class, 'certification_id')
            ->where('is_active', true);
    }

    public function userCertifications()
    {
        return $this->hasMany(TrainingUserCertification::class, 'certification_id');
    }
}
