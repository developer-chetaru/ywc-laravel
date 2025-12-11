<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RetirementNeedsCalculator extends Component
{
    // Inputs
    public $current_age = 30;
    public $retirement_age = 65;
    public $life_expectancy = 85;
    public $desired_annual_income = 50000;
    public $current_savings = 10000;
    public $monthly_contribution = 500;
    public $expected_return = 7;
    public $inflation_rate = 3;

    // Results
    public $total_needed = 0;
    public $projected_amount = 0;
    public $gap = 0;
    public $adjusted_monthly_contribution = 0;
    public $is_on_track = false;
    public $show_results = false;

    public function calculate()
    {
        // Validate inputs
        $this->validate([
            'current_age' => 'required|numeric|min:18|max:100',
            'retirement_age' => 'required|numeric|min:' . ($this->current_age + 1) . '|max:100',
            'life_expectancy' => 'required|numeric|min:' . ($this->retirement_age + 1) . '|max:120',
            'desired_annual_income' => 'required|numeric|min:0',
            'current_savings' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            'expected_return' => 'required|numeric|min:0|max:20',
            'inflation_rate' => 'required|numeric|min:0|max:10',
        ]);

        $years_to_retirement = $this->retirement_age - $this->current_age;
        $years_in_retirement = $this->life_expectancy - $this->retirement_age;

        // Calculate total amount needed at retirement (accounting for inflation)
        $annual_income_in_future_dollars = $this->desired_annual_income * pow(1 + ($this->inflation_rate / 100), $years_to_retirement);
        
        // Using 4% rule (withdrawal rate) to calculate needed capital
        $this->total_needed = ($annual_income_in_future_dollars / 0.04) * $years_in_retirement;

        // Calculate projected amount based on current plan
        $monthly_rate = ($this->expected_return / 100) / 12;
        $months = $years_to_retirement * 12;

        // Future value of current savings
        $fv_current = $this->current_savings * pow(1 + ($this->expected_return / 100), $years_to_retirement);

        // Future value of monthly contributions (annuity)
        if ($monthly_rate > 0) {
            $fv_contributions = $this->monthly_contribution * ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        } else {
            $fv_contributions = $this->monthly_contribution * $months;
        }

        $this->projected_amount = $fv_current + $fv_contributions;

        // Calculate gap
        $this->gap = $this->total_needed - $this->projected_amount;
        $this->is_on_track = $this->gap <= 0;

        // Calculate required monthly contribution to meet goal
        if ($this->gap > 0 && $monthly_rate > 0) {
            $fv_gap = $this->gap / pow(1 + ($this->expected_return / 100), $years_to_retirement);
            $this->adjusted_monthly_contribution = $fv_gap / ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
            $this->adjusted_monthly_contribution = max(0, $this->adjusted_monthly_contribution);
        } else {
            $this->adjusted_monthly_contribution = 0;
        }

        $this->show_results = true;

        // Save calculation (for both logged in users and guests)
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'retirement_needs',
                'input_data' => [
                    'current_age' => $this->current_age,
                    'retirement_age' => $this->retirement_age,
                    'life_expectancy' => $this->life_expectancy,
                    'desired_annual_income' => $this->desired_annual_income,
                    'current_savings' => $this->current_savings,
                    'monthly_contribution' => $this->monthly_contribution,
                    'expected_return' => $this->expected_return,
                    'inflation_rate' => $this->inflation_rate,
                ],
                'result_data' => [
                    'total_needed' => $this->total_needed,
                    'projected_amount' => $this->projected_amount,
                    'gap' => $this->gap,
                    'adjusted_monthly_contribution' => $this->adjusted_monthly_contribution,
                    'is_on_track' => $this->is_on_track,
                ],
                'session_id' => session()->getId(),
            ]);
        } else {
            // Save for guest users (lead capture)
            FinancialCalculation::create([
                'user_id' => null,
                'calculator_type' => 'retirement_needs',
                'input_data' => [
                    'current_age' => $this->current_age,
                    'retirement_age' => $this->retirement_age,
                    'life_expectancy' => $this->life_expectancy,
                    'desired_annual_income' => $this->desired_annual_income,
                    'current_savings' => $this->current_savings,
                    'monthly_contribution' => $this->monthly_contribution,
                    'expected_return' => $this->expected_return,
                    'inflation_rate' => $this->inflation_rate,
                ],
                'result_data' => [
                    'total_needed' => $this->total_needed,
                    'projected_amount' => $this->projected_amount,
                    'gap' => $this->gap,
                    'adjusted_monthly_contribution' => $this->adjusted_monthly_contribution,
                    'is_on_track' => $this->is_on_track,
                ],
                'session_id' => session()->getId(),
                'is_saved' => false,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.retirement-needs-calculator');
    }
}
