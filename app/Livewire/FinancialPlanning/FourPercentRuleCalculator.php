<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class FourPercentRuleCalculator extends Component
{
    public $desired_annual_income = 50000;
    public $current_savings = 10000;
    public $monthly_contribution = 500;
    public $years_until_retirement = 30;
    public $expected_return = 7;
    public $withdrawal_rate = 4;

    public $total_needed = 0;
    public $projected_amount = 0;
    public $gap = 0;
    public $is_achievable = false;
    public $adjusted_monthly_contribution = 0;
    public $monthly_withdrawal = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'desired_annual_income' => 'required|numeric|min:0',
            'current_savings' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            'years_until_retirement' => 'required|numeric|min:1|max:60',
            'expected_return' => 'required|numeric|min:0|max:20',
            'withdrawal_rate' => 'required|numeric|min:1|max:10',
        ]);

        // Calculate total needed using 4% rule (or custom withdrawal rate)
        $this->total_needed = ($this->desired_annual_income / ($this->withdrawal_rate / 100));
        $this->monthly_withdrawal = $this->desired_annual_income / 12;

        // Calculate projected amount
        $monthly_rate = ($this->expected_return / 100) / 12;
        $months = $this->years_until_retirement * 12;
        $fv_current = $this->current_savings * pow(1 + ($this->expected_return / 100), $this->years_until_retirement);

        if ($monthly_rate > 0) {
            $fv_contributions = $this->monthly_contribution * ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        } else {
            $fv_contributions = $this->monthly_contribution * $months;
        }

        $this->projected_amount = $fv_current + $fv_contributions;
        $this->gap = $this->total_needed - $this->projected_amount;
        $this->is_achievable = $this->gap <= 0;

        // Calculate required contribution
        if ($this->gap > 0 && $monthly_rate > 0) {
            $fv_gap = $this->gap / pow(1 + ($this->expected_return / 100), $this->years_until_retirement);
            $this->adjusted_monthly_contribution = $fv_gap / ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        }

        $this->show_results = true;

        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'four_percent_rule',
                'input_data' => $this->only(['desired_annual_income', 'current_savings', 'monthly_contribution', 'years_until_retirement', 'expected_return', 'withdrawal_rate']),
                'result_data' => [
                    'total_needed' => $this->total_needed,
                    'projected_amount' => $this->projected_amount,
                    'gap' => $this->gap,
                    'is_achievable' => $this->is_achievable,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.four-percent-rule-calculator');
    }
}
