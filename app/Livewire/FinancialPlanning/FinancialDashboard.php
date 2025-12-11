<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialAccount;
use App\Models\FinancialGoal;
use App\Models\FinancialTransaction;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class FinancialDashboard extends Component
{
    public $net_worth = 0;
    public $total_assets = 0;
    public $total_debts = 0;
    public $total_income = 0;
    public $total_expenses = 0;
    public $monthly_cash_flow = 0;
    public $accounts = [];
    public $goals = [];
    public $recent_transactions = [];
    public $recent_calculations = [];
    public $goal_progress = [];
    public $net_worth_history = [];
    public $income_expense_data = [];
    public $expense_by_category = [];
    public $income_by_category = [];

    public function mount()
    {
        Log::info('FinancialDashboard mount: Starting', [
            'user_id' => Auth::id(),
            'authenticated' => Auth::check(),
            'url' => request()->fullUrl(),
            'path' => request()->path(),
            'route_name' => request()->route() ? request()->route()->getName() : 'null',
        ]);

        if (!Auth::check()) {
            Log::warning('FinancialDashboard mount: Not authenticated - redirecting to login', [
                'session_id' => session()->getId(),
                'all_session' => session()->all(),
            ]);
            // Use Livewire redirect
            return $this->redirect(route('login'), navigate: true);
        }

        Log::info('FinancialDashboard mount: Loading data');
        $this->loadDashboardData();
        Log::info('FinancialDashboard mount: Data loaded successfully');
    }

    private function loadDashboardData()
    {
        $user = Auth::user();

        // Load accounts
        $this->accounts = FinancialAccount::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        // Calculate net worth
        $this->total_assets = $this->accounts
            ->whereIn('type', ['savings', 'checking', 'investment', 'pension', 'property', 'other'])
            ->sum('current_balance');

        $this->total_debts = abs($this->accounts
            ->where('type', 'debt')
            ->sum('current_balance'));

        $this->net_worth = $this->total_assets - $this->total_debts;

        // Load goals
        $this->goals = FinancialGoal::where('user_id', $user->id)
            ->orderBy('priority', 'desc')
            ->orderBy('target_date', 'asc')
            ->limit(5)
            ->get();

        // Calculate goal progress
        foreach ($this->goals as $goal) {
            $this->goal_progress[$goal->id] = [
                'progress' => $goal->progress_percentage,
                'remaining' => max(0, $goal->target_amount - $goal->current_amount),
                'months_remaining' => now()->diffInMonths($goal->target_date),
            ];
        }

        // Load recent transactions
        $this->recent_transactions = FinancialTransaction::where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->with('account')
            ->get();

        // Calculate income/expenses for current month
        $currentMonth = now()->startOfMonth();
        $monthlyIncome = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->where('transaction_date', '>=', $currentMonth)
            ->sum('amount');

        $monthlyExpenses = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $currentMonth)
            ->sum('amount');

        $this->total_income = $monthlyIncome;
        $this->total_expenses = $monthlyExpenses;
        $this->monthly_cash_flow = $monthlyIncome - $monthlyExpenses;

        // Load recent calculations
        $this->recent_calculations = FinancialCalculation::where('user_id', $user->id)
            ->where('is_saved', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Prepare chart data - Net worth history (last 6 months)
        $months = [];
        $netWorthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            // Calculate net worth for this month (simplified - using current accounts)
            // In production, you'd track historical snapshots
            $netWorthData[] = $this->net_worth;
        }
        $this->net_worth_history = [
            'labels' => $months,
            'data' => $netWorthData,
        ];

        // Income vs Expense (last 6 months)
        $incomeData = [];
        $expenseData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $incomeData[] = FinancialTransaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $expenseData[] = FinancialTransaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');
        }
        $this->income_expense_data = [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData,
        ];

        // Expense by category (current month)
        $this->expense_by_category = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $currentMonth)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Income by category (current month)
        $this->income_by_category = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->where('transaction_date', '>=', $currentMonth)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();
    }

    public function deleteGoal($goalId)
    {
        $goal = FinancialGoal::where('id', $goalId)
            ->where('user_id', Auth::id())
            ->first();

        if ($goal) {
            $goal->delete();
            $this->loadDashboardData();
            session()->flash('message', 'Goal deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.financial-dashboard');
    }
}

