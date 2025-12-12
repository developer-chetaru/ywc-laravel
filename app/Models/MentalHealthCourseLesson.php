<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthCourseLesson extends Model
{
    protected $table = 'mental_health_course_lessons';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'lesson_type',
        'content',
        'video_path',
        'audio_path',
        'duration_minutes',
        'quiz_data',
        'exercise_data',
        'has_subtitles',
        'subtitle_languages',
        'transcript_path',
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'exercise_data' => 'array',
        'subtitle_languages' => 'array',
        'has_subtitles' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(MentalHealthCourse::class, 'course_id');
    }
}
