<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentalHealthSessionBooking extends Model
{
    protected $table = 'mental_health_session_bookings';

    protected $fillable = [
        'user_id',
        'therapist_id',
        'session_id',
        'session_type',
        'duration_minutes',
        'scheduled_at',
        'timezone',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
        'recurring_session_count',
        'recurring_series_id',
        'requires_approval',
        'request_status',
        'request_expires_at',
        'session_cost',
        'credits_used',
        'amount_paid',
        'user_notes',
        'therapist_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'recurring_end_date' => 'date',
        'request_expires_at' => 'datetime',
        'is_recurring' => 'boolean',
        'session_cost' => 'decimal:2',
        'credits_used' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(MentalHealthTherapist::class, 'therapist_id');
    }

    public function session(): HasOne
    {
        return $this->hasOne(MentalHealthSession::class, 'booking_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(MentalHealthPayment::class, 'booking_id');
    }

    public function recurringSeries(): BelongsTo
    {
        return $this->belongsTo(MentalHealthSessionBooking::class, 'recurring_series_id');
    }

    public function recurringSessions(): HasMany
    {
        return $this->hasMany(MentalHealthSessionBooking::class, 'recurring_series_id');
    }
}
