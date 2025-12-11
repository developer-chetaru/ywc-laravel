<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'account_subtype',
        'current_balance',
        'institution',
        'account_number',
        'interest_rate',
        'monthly_contribution',
        'asset_allocation',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_contribution' => 'decimal:2',
        'asset_allocation' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'account_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(FinancialGoal::class, 'linked_account_id');
    }
}
