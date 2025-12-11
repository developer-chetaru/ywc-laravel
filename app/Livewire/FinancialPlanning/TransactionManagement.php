<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialTransaction;
use App\Models\FinancialAccount;
use App\Models\FinancialGoal;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TransactionManagement extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingId = null;
    public $filterType = 'all'; // all, income, expense
    public $filterPeriod = 'all'; // all, working, time_off
    
    // Form fields
    public $type = 'income';
    public $transaction_date = '';
    public $amount = 0;
    public $category = '';
    public $description = '';
    public $period_type = 'both';
    public $account_id = null;
    public $goal_id = null;
    public $is_recurring = false;
    public $recurring_frequency = null;

    protected $rules = [
        'type' => 'required|in:income,expense',
        'transaction_date' => 'required|date',
        'amount' => 'required|numeric|min:0.01|max:999999999.99',
        'category' => 'required|string|max:255',
        'description' => 'nullable|string',
        'period_type' => 'required|in:working,time_off,both',
        'account_id' => 'nullable|exists:financial_accounts,id',
        'goal_id' => 'nullable|exists:financial_goals,id',
        'is_recurring' => 'boolean',
        'recurring_frequency' => 'nullable|in:monthly,quarterly,yearly',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterPeriod()
    {
        $this->resetPage();
    }

    public function openForm($transactionId = null)
    {
        if ($transactionId) {
            $transaction = FinancialTransaction::where('id', $transactionId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $this->editingId = $transaction->id;
            $this->type = $transaction->type;
            $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
            $this->amount = $transaction->amount;
            $this->category = $transaction->category;
            $this->description = $transaction->description ?? '';
            $this->period_type = $transaction->period_type;
            $this->account_id = $transaction->account_id;
            $this->goal_id = $transaction->goal_id;
            $this->is_recurring = $transaction->is_recurring;
            $this->recurring_frequency = $transaction->recurring_frequency;
        } else {
            $this->resetForm();
        }
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->type = 'income';
        $this->transaction_date = now()->format('Y-m-d');
        $this->amount = 0;
        $this->category = '';
        $this->description = '';
        $this->period_type = 'both';
        $this->account_id = null;
        $this->goal_id = null;
        $this->is_recurring = false;
        $this->recurring_frequency = null;
    }

    public function save()
    {
        $this->validate();

        // Ensure account belongs to user
        if ($this->account_id) {
            $account = FinancialAccount::where('id', $this->account_id)
                ->where('user_id', Auth::id())
                ->first();
            if (!$account) {
                $this->addError('account_id', 'Invalid account selected.');
                return;
            }
        }

        // Ensure goal belongs to user
        if ($this->goal_id) {
            $goal = FinancialGoal::where('id', $this->goal_id)
                ->where('user_id', Auth::id())
                ->first();
            if (!$goal) {
                $this->addError('goal_id', 'Invalid goal selected.');
                return;
            }
        }

        $data = [
            'user_id' => Auth::id(),
            'type' => $this->type,
            'transaction_date' => $this->transaction_date,
            'amount' => $this->amount,
            'category' => $this->category,
            'description' => $this->description ?: null,
            'period_type' => $this->period_type,
            'account_id' => $this->account_id,
            'goal_id' => $this->goal_id,
            'is_recurring' => $this->is_recurring,
            'recurring_frequency' => $this->recurring_frequency,
        ];

        if ($this->editingId) {
            FinancialTransaction::where('id', $this->editingId)
                ->where('user_id', Auth::id())
                ->update($data);
            session()->flash('message', 'Transaction updated successfully.');
        } else {
            FinancialTransaction::create($data);
            session()->flash('message', 'Transaction created successfully.');
        }

        $this->closeForm();
        $this->resetPage();
    }

    public function delete($transactionId)
    {
        $transaction = FinancialTransaction::where('id', $transactionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $transaction->delete();
        session()->flash('message', 'Transaction deleted successfully.');
        $this->resetPage();
    }

    public function render()
    {
        $query = FinancialTransaction::where('user_id', Auth::id());

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterPeriod !== 'all') {
            $query->where(function($q) {
                $q->where('period_type', $this->filterPeriod)
                  ->orWhere('period_type', 'both');
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->with(['account', 'goal'])
            ->paginate(20);

        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $goals = FinancialGoal::where('user_id', Auth::id())
            ->get();

        $incomeCategories = [
            'salary' => 'Salary',
            'tips' => 'Tips',
            'bonus' => 'Bonus',
            'charter_bonus' => 'Charter Bonus',
            'investment_income' => 'Investment Income',
            'other' => 'Other Income',
        ];

        $expenseCategories = [
            'accommodation' => 'Accommodation',
            'food_dining' => 'Food & Dining',
            'transportation' => 'Transportation',
            'entertainment' => 'Entertainment',
            'personal_care' => 'Personal Care',
            'insurance' => 'Insurance',
            'subscriptions' => 'Subscriptions',
            'shopping' => 'Shopping',
            'travel' => 'Travel',
            'utilities' => 'Utilities',
            'other' => 'Other Expense',
        ];

        $categories = $this->type === 'income' ? $incomeCategories : $expenseCategories;

        return view('livewire.financial-planning.transaction-management', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'goals' => $goals,
            'categories' => $categories,
        ]);
    }
}

