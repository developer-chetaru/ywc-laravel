<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialBudget;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class BudgetManagement extends Component
{
    public $budgets = [];
    public $currentBudget = null;
    public $selectedPeriod = 'current';

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->loadBudgets();
    }

    public function loadBudgets()
    {
        $user = Auth::user();
        
        // Get or create current month budget
        $now = now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        $this->currentBudget = FinancialBudget::where('user_id', $user->id)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where('is_active', true)
            ->first();

        if (!$this->currentBudget) {
            // Auto-create budget based on average spending
            $this->createDefaultBudget($startDate, $endDate);
        }

        // Load actuals
        if ($this->currentBudget) {
            $this->currentBudget->load('user');
        }
    }

    private function createDefaultBudget($startDate, $endDate)
    {
        $user = Auth::user();
        
        // Calculate average income and expenses from last 3 months
        $threeMonthsAgo = now()->subMonths(3)->startOfMonth();
        
        $avgIncome = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->where('transaction_date', '>=', $threeMonthsAgo)
            ->avg('amount') * 4; // Average per month * 4 = estimate

        $avgExpenses = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $threeMonthsAgo)
            ->avg('amount') * 4;

        // Calculate category budgets
        $categoryBudgets = [];
        $categories = FinancialTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $threeMonthsAgo)
            ->selectRaw('category, AVG(amount) * 4 as avg_monthly')
            ->groupBy('category')
            ->get();

        foreach ($categories as $cat) {
            $categoryBudgets[$cat->category] = round($cat->avg_monthly, 2);
        }

        $this->currentBudget = FinancialBudget::create([
            'user_id' => $user->id,
            'name' => $startDate->format('F Y') . ' Budget',
            'period' => 'monthly',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_income' => $avgIncome ?? 0,
            'total_expenses' => $avgExpenses ?? 0,
            'savings_target' => ($avgIncome ?? 0) - ($avgExpenses ?? 0),
            'category_budgets' => $categoryBudgets,
            'is_active' => true,
        ]);
    }

    public function render()
    {
        if ($this->currentBudget) {
            $actualIncome = FinancialTransaction::where('user_id', Auth::id())
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$this->currentBudget->start_date, $this->currentBudget->end_date])
                ->sum('amount');

            $actualExpenses = FinancialTransaction::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$this->currentBudget->start_date, $this->currentBudget->end_date])
                ->sum('amount');

            $categoryActuals = FinancialTransaction::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$this->currentBudget->start_date, $this->currentBudget->end_date])
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
                ->toArray();
        } else {
            $actualIncome = 0;
            $actualExpenses = 0;
            $categoryActuals = [];
        }

        return view('livewire.financial-planning.budget-management', [
            'actualIncome' => $actualIncome,
            'actualExpenses' => $actualExpenses,
            'categoryActuals' => $categoryActuals,
        ]);
    }
}

