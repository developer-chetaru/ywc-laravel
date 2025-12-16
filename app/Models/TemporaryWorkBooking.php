<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\TemporaryWorkBookingFactory;

class TemporaryWorkBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return TemporaryWorkBookingFactory::new();
    }

    protected $fillable = [
        'job_post_id',
        'user_id',
        'booked_by_user_id',
        'status',
        'work_date',
        'start_time',
        'end_time',
        'total_hours',
        'work_description',
        'requirements',
        'location',
        'berth_details',
        'latitude',
        'longitude',
        'day_rate',
        'hourly_rate',
        'total_payment',
        'payment_currency',
        'payment_method',
        'payment_timing',
        'payment_received',
        'payment_received_at',
        'contact_name',
        'contact_phone',
        'contact_email',
        'whatsapp_available',
        'confirmed_at',
        'started_at',
        'completed_at',
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',
        'hours_before_start',
        'crew_rated_vessel',
        'vessel_rated_crew',
        'crew_notes',
        'vessel_notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'day_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'total_payment' => 'decimal:2',
        'requirements' => 'array',
        'payment_received' => 'boolean',
        'whatsapp_available' => 'boolean',
        'confirmed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_received_at' => 'datetime',
        'crew_rated_vessel' => 'boolean',
        'vessel_rated_crew' => 'boolean',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function ratings()
    {
        return $this->hasMany(JobRating::class);
    }

    // Helper methods
    public function markAsPaid(): void
    {
        $this->update([
            'payment_received' => true,
            'payment_received_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function isPendingPayment(): bool
    {
        return $this->status === 'completed' && !$this->payment_received;
    }

    public function isPaid(): bool
    {
        return $this->payment_received === true;
    }
}
