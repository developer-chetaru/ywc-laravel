<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialConsultation extends Model
{
    protected $fillable = [
        'user_id',
        'advisor_id',
        'type',
        'status',
        'scheduled_at',
        'meeting_link',
        'pre_consultation_notes',
        'consultation_notes',
        'action_plan',
        'recording_url',
        'amount',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(FinancialAdvisor::class, 'advisor_id');
    }
}

