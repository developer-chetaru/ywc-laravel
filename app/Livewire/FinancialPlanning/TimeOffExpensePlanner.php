<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class TimeOffExpensePlanner extends Component
{
    // Inputs
    public $time_off_months = 4;
    public $rent_monthly = 1000;
    public $utilities_monthly = 150;
    public $food_monthly = 500;
    public $transportation_monthly = 300;
    public $travel_budget = 2000;
    public $insurance_monthly = 200;
    public $entertainment_monthly = 400;
    public $healthcare_monthly = 150;
    public $other_expenses_monthly = 200;
    public $current_savings = 5000;
    public $savings_per_month = 1000;

    // Results
    public $monthly_total = 0;
    public $total_needed = 0;
    public $savings_required = 0;
    public $months_to_save = 0;
    public $monthly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'time_off_months' => 'required|numeric|min:1|max:12',
            'rent_monthly' => 'required|numeric|min:0',
            'utilities_monthly' => 'required|numeric|min:0',
            'food_monthly' => 'required|numeric|min:0',
            'transportation_monthly' => 'required|numeric|min:0',
            'travel_budget' => 'required|numeric|min:0',
            'insurance_monthly' => 'required|numeric|min:0',
            'entertainment_monthly' => 'required|numeric|min:0',
            'healthcare_monthly' => 'required|numeric|min:0',
            'other_expenses_monthly' => 'required|numeric|min:0',
            'current_savings' => 'required|numeric|min:0',
            'savings_per_month' => 'required|numeric|min:0',
        ]);

        // Calculate monthly expenses
        $this->monthly_total = $this->rent_monthly + $this->utilities_monthly + 
                              $this->food_monthly + $this->transportation_monthly + 
                              $this->insurance_monthly + $this->entertainment_monthly + 
                              $this->healthcare_monthly + $this->other_expenses_monthly;

        // Total needed (monthly expenses * months + travel budget)
        $this->total_needed = ($this->monthly_total * $this->time_off_months) + $this->travel_budget;
        
        // Calculate savings needed
        $this->savings_required = max(0, $this->total_needed - $this->current_savings);
        
        // Calculate months to save
        $this->months_to_save = $this->savings_per_month > 0 ? 
            ceil($this->savings_required / $this->savings_per_month) : 0;

        // Monthly breakdown
        $this->monthly_breakdown = [
            ['category' => 'Rent', 'amount' => $this->rent_monthly],
            ['category' => 'Utilities', 'amount' => $this->utilities_monthly],
            ['category' => 'Food', 'amount' => $this->food_monthly],
            ['category' => 'Transportation', 'amount' => $this->transportation_monthly],
            ['category' => 'Insurance', 'amount' => $this->insurance_monthly],
            ['category' => 'Entertainment', 'amount' => $this->entertainment_monthly],
            ['category' => 'Healthcare', 'amount' => $this->healthcare_monthly],
            ['category' => 'Other Expenses', 'amount' => $this->other_expenses_monthly],
        ];

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'time_off_expense',
                'input_data' => [
                    'time_off_months' => $this->time_off_months,
                    'expenses' => [
                        'rent' => $this->rent_monthly,
                        'utilities' => $this->utilities_monthly,
                        'food' => $this->food_monthly,
                        'transportation' => $this->transportation_monthly,
                        'travel_budget' => $this->travel_budget,
                        'insurance' => $this->insurance_monthly,
                        'entertainment' => $this->entertainment_monthly,
                        'healthcare' => $this->healthcare_monthly,
                        'other' => $this->other_expenses_monthly,
                    ],
                    'current_savings' => $this->current_savings,
                    'savings_per_month' => $this->savings_per_month,
                ],
                'result_data' => [
                    'monthly_total' => $this->monthly_total,
                    'total_needed' => $this->total_needed,
                    'savings_required' => $this->savings_required,
                    'months_to_save' => $this->months_to_save,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.time-off-expense-planner');
    }
}
