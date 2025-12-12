<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentalHealthCourse extends Model
{
    use SoftDeletes;

    protected $table = 'mental_health_courses';

    protected $fillable = [
        'title',
        'description',
        'category',
        'modules',
        'total_duration_minutes',
        'difficulty_level',
        'prerequisites',
        'certificate_available',
        'status',
        'created_by',
    ];

    protected $casts = [
        'modules' => 'array',
        'prerequisites' => 'array',
        'certificate_available' => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(MentalHealthCourseLesson::class, 'course_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(MentalHealthCourseEnrollment::class, 'course_id');
    }
}
