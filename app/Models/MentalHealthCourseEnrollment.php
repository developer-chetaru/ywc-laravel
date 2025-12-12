<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthCourseEnrollment extends Model
{
    protected $table = 'mental_health_course_enrollments';

    protected $fillable = [
        'user_id',
        'course_id',
        'progress_percentage',
        'current_lesson_id',
        'started_at',
        'completed_at',
        'certificate_issued',
        'certificate_path',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(MentalHealthCourse::class, 'course_id');
    }
}
