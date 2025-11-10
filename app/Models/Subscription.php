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
        'stripe_session_id',
        'stripe_subscription_id',
        'amount',
        'status',
        'interval',
        'interval_count',
        'start_date',
        'end_date',
    ];

    protected $dates = ['start_date', 'end_date'];
  
  public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->end_date
            && $this->end_date->isFuture();
    }
  
}
