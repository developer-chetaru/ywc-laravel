<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourseLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_course_id',
        'name',
        'address',
        'city',
        'state_province',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'region',
        'directions',
        'parking_info',
        'accommodation_nearby',
        'photos',
        'is_primary',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'photos' => 'array',
        'is_primary' => 'boolean',
    ];

    public function providerCourse()
    {
        return $this->belongsTo(TrainingProviderCourse::class, 'provider_course_id');
    }

    public function schedules()
    {
        return $this->hasMany(TrainingCourseSchedule::class, 'location_id');
    }
}
