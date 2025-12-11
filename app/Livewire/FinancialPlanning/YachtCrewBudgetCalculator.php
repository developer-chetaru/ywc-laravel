<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class YachtCrewBudgetCalculator extends Component
{
    // Working Period
    public $monthly_salary = 4000;
    public $tips_per_month = 500;
    public $working_months_per_year = 8;
    
    // Working Expenses (minimal on yacht)
    public $working_expenses_monthly = 100; // Phone, personal items
    public $travel_to_yacht = 500; // One-time per contract
    
    // Time Off
    public $time_off_months_per_year = 4;
    public $rent_monthly = 800;
    public $utilities_monthly = 150;
    public $food_time_off_monthly = 400;
    public $transportation_monthly = 200;
    public $insurance_monthly = 150;
    public $other_expenses_monthly = 300;
    
    // Savings Goals
    public $savings_rate_target = 30; // Percentage of income to save
    
    // Results
    public $annual_income = 0;
    public $annual_expenses = 0;
    public $annual_savings = 0;
    public $actual_savings_rate = 0;
    public $time_off_monthly_expenses = 0;
    public $working_monthly_net = 0;
    public $monthly_budget_summary = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'monthly_salary' => 'required|numeric|min:0',
            'tips_per_month' => 'required|numeric|min:0',
            'working_months_per_year' => 'required|numeric|min:1|max:12',
            'working_expenses_monthly' => 'required|numeric|min:0',
            'travel_to_yacht' => 'required|numeric|min:0',
            'time_off_months_per_year' => 'required|numeric|min:0|max:12',
            'rent_monthly' => 'required|numeric|min:0',
            'utilities_monthly' => 'required|numeric|min:0',
            'food_time_off_monthly' => 'required|numeric|min:0',
            'transportation_monthly' => 'required|numeric|min:0',
            'insurance_monthly' => 'required|numeric|min:0',
            'other_expenses_monthly' => 'required|numeric|min:0',
            'savings_rate_target' => 'required|numeric|min:0|max:100',
        ]);

        // Calculate annual income
        $this->annual_income = ($this->monthly_salary + $this->tips_per_month) * $this->working_months_per_year;
        
        // Calculate time off monthly expenses
        $this->time_off_monthly_expenses = $this->rent_monthly + $this->utilities_monthly + 
                                          $this->food_time_off_monthly + $this->transportation_monthly + 
                                          $this->insurance_monthly + $this->other_expenses_monthly;
        
        // Calculate annual expenses
        $working_expenses_annual = ($this->working_expenses_monthly * $this->working_months_per_year) + 
                                   ($this->travel_to_yacht * ($this->working_months_per_year > 0 ? 1 : 0));
        $time_off_expenses_annual = $this->time_off_monthly_expenses * $this->time_off_months_per_year;
        $this->annual_expenses = $working_expenses_annual + $time_off_expenses_annual;
        
        // Calculate savings
        $this->annual_savings = $this->annual_income - $this->annual_expenses;
        $this->actual_savings_rate = $this->annual_income > 0 ? 
            ($this->annual_savings / $this->annual_income) * 100 : 0;
        
        // Working monthly net (after minimal expenses)
        $this->working_monthly_net = ($this->monthly_salary + $this->tips_per_month) - $this->working_expenses_monthly;
        
        // Monthly breakdown
        $this->monthly_budget_summary = [
            'working' => [
                'income' => $this->monthly_salary + $this->tips_per_month,
                'expenses' => $this->working_expenses_monthly,
                'net' => $this->working_monthly_net,
            ],
            'time_off' => [
                'income' => 0,
                'expenses' => $this->time_off_monthly_expenses,
                'net' => -$this->time_off_monthly_expenses,
            ],
        ];

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'yacht_crew_budget',
                'input_data' => [
                    'monthly_salary' => $this->monthly_salary,
                    'tips_per_month' => $this->tips_per_month,
                    'working_months_per_year' => $this->working_months_per_year,
                    'time_off_months_per_year' => $this->time_off_months_per_year,
                    'working_expenses_monthly' => $this->working_expenses_monthly,
                    'time_off_expenses' => [
                        'rent' => $this->rent_monthly,
                        'utilities' => $this->utilities_monthly,
                        'food' => $this->food_time_off_monthly,
                        'transportation' => $this->transportation_monthly,
                        'insurance' => $this->insurance_monthly,
                        'other' => $this->other_expenses_monthly,
                    ],
                ],
                'result_data' => [
                    'annual_income' => $this->annual_income,
                    'annual_expenses' => $this->annual_expenses,
                    'annual_savings' => $this->annual_savings,
                    'actual_savings_rate' => $this->actual_savings_rate,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.yacht-crew-budget-calculator');
    }
}
