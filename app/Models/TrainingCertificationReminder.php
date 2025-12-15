<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCertificationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_certification_id',
        'reminder_type',
        'reminder_date',
        'is_sent',
        'sent_at',
        'email_content',
        'course_recommendations',
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
        'course_recommendations' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userCertification()
    {
        return $this->belongsTo(TrainingUserCertification::class, 'user_certification_id');
    }
}
