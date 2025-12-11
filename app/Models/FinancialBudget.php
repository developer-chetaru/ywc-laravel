<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialBudget extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'period',
        'start_date',
        'end_date',
        'total_income',
        'total_expenses',
        'savings_target',
        'category_budgets',
        'is_active',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'savings_target' => 'decimal:2',
        'category_budgets' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActualExpensesAttribute()
    {
        return \App\Models\FinancialTransaction::where('user_id', $this->user_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->sum('amount');
    }

    public function getActualIncomeAttribute()
    {
        return \App\Models\FinancialTransaction::where('user_id', $this->user_id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->sum('amount');
    }
}

