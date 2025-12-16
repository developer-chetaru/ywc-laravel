<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_post_id',
        'job_application_id',
        'temporary_work_booking_id',
        'sender_id',
        'receiver_id',
        'message',
        'subject',
        'attachments',
        'is_read',
        'read_at',
        'is_flagged',
        'flag_reason',
        'message_type',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'is_flagged' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function temporaryWorkBooking()
    {
        return $this->belongsTo(TemporaryWorkBooking::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
