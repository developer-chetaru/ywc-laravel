<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialAccount;
use App\Models\FinancialGoal;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RetirementPlanner extends Component
{
    // User inputs
    public $current_age = 30;
    public $target_retirement_age = 65;
    public $life_expectancy = 85;
    public $desired_annual_income = 50000;
    public $current_savings = 0;
    public $monthly_contribution = 1000;
    public $expected_return_rate = 7;
    public $inflation_rate = 3;

    // Results
    public $total_needed = 0;
    public $projected_amount = 0;
    public $gap = 0;
    public $required_monthly_contribution = 0;
    public $year_by_year = [];
    public $readiness_score = 0;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->loadUserData();
        $this->calculate();
    }

    public function loadUserData()
    {
        $user = Auth::user();
        
        // Load current savings from accounts
        $this->current_savings = FinancialAccount::where('user_id', $user->id)
            ->whereIn('type', ['savings', 'investment', 'pension'])
            ->where('is_active', true)
            ->sum('current_balance');

        // Load retirement goal if exists
        $retirementGoal = FinancialGoal::where('user_id', $user->id)
            ->where('type', 'retirement')
            ->first();

        if ($retirementGoal) {
            $this->desired_annual_income = $retirementGoal->target_amount / 25; // 4% rule
            $this->target_retirement_age = now()->diffInYears($retirementGoal->target_date) + $this->current_age;
            $this->monthly_contribution = $retirementGoal->monthly_contribution ?? $this->monthly_contribution;
        }
    }

    public function calculate()
    {
        // Calculate total needed at retirement (using 4% rule)
        $years_in_retirement = $this->life_expectancy - $this->target_retirement_age;
        $this->total_needed = $this->desired_annual_income * 25; // 4% rule

        // Project current savings growth
        $years_to_retirement = $this->target_retirement_age - $this->current_age;
        $monthly_rate = ($this->expected_return_rate / 100) / 12;
        $months = $years_to_retirement * 12;

        // Future value of current savings
        $fv_current = $this->current_savings * pow(1 + ($this->expected_return_rate / 100), $years_to_retirement);

        // Future value of monthly contributions
        if ($this->monthly_contribution > 0 && $monthly_rate > 0) {
            $fv_contributions = $this->monthly_contribution * 
                ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        } else {
            $fv_contributions = $this->monthly_contribution * $months;
        }

        $this->projected_amount = $fv_current + $fv_contributions;
        $this->gap = $this->total_needed - $this->projected_amount;

        // Calculate required monthly contribution to meet goal
        if ($this->gap > 0 && $monthly_rate > 0) {
            $fv_needed = $this->total_needed - $fv_current;
            $this->required_monthly_contribution = $fv_needed / 
                ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        } else {
            $this->required_monthly_contribution = 0;
        }

        // Generate year-by-year projection
        $this->year_by_year = [];
        $balance = $this->current_savings;
        for ($year = $this->current_age; $year <= $this->target_retirement_age; $year++) {
            $balance = $balance * (1 + ($this->expected_return_rate / 100)) + 
                      ($this->monthly_contribution * 12);
            $this->year_by_year[] = [
                'age' => $year,
                'balance' => $balance,
                'needed' => $year == $this->target_retirement_age ? $this->total_needed : 0,
            ];
        }

        // Calculate readiness score (0-100)
        $progress = $this->projected_amount / $this->total_needed;
        $this->readiness_score = min(100, max(0, $progress * 100));
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['current_age', 'target_retirement_age', 'life_expectancy', 
            'desired_annual_income', 'current_savings', 'monthly_contribution', 'expected_return_rate', 'inflation_rate'])) {
            $this->calculate();
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.retirement-planner');
    }
}

