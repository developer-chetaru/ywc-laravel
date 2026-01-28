<?php

// app/Models/Subscription.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_customer_id',
        'stripe_session_id',
        'stripe_subscription_id',
        'plan_type',
        'amount',
        'status',
        'interval',
        'interval_count',
        'start_date',
        'end_date',
        'current_period_end',
        'cancel_at_period_end',
        'grace_period_end',
        'payment_retry_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'current_period_end' => 'datetime',
        'grace_period_end' => 'datetime',
        'cancel_at_period_end' => 'boolean',
        'payment_retry_count' => 'integer',
    ];
  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->current_period_end
            && $this->current_period_end->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return $this->grace_period_end
            && $this->grace_period_end->isFuture()
            && $this->status === 'past_due';
    }

    public function isCancelled(): bool
    {
        return $this->cancel_at_period_end === true;
    }

    public function canReactivate(): bool
    {
        return $this->cancel_at_period_end === true
            && $this->current_period_end
            && $this->current_period_end->isFuture();
    }
  
}
