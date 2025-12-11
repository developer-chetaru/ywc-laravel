<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'transaction_date',
        'amount',
        'category',
        'description',
        'period_type',
        'account_id',
        'goal_id',
        'receipt_path',
        'is_recurring',
        'recurring_frequency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(FinancialGoal::class);
    }
}
