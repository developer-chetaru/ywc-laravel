<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PensionGrowthCalculator extends Component
{
    public $current_balance = 10000;
    public $monthly_contribution = 500;
    public $years_until_retirement = 25;
    public $expected_return = 6;
    public $tax_relief_rate = 20;
    public $include_tax_relief = true;

    public $future_value = 0;
    public $total_contributions = 0;
    public $growth_amount = 0;
    public $tax_relief_amount = 0;
    public $yearly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'current_balance' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            'years_until_retirement' => 'required|numeric|min:1|max:60',
            'expected_return' => 'required|numeric|min:0|max:20',
            'tax_relief_rate' => 'nullable|numeric|min:0|max:50',
        ]);

        $monthly_rate = ($this->expected_return / 100) / 12;
        $months = $this->years_until_retirement * 12;

        // Calculate tax relief on contributions if applicable
        $effective_monthly_contribution = $this->monthly_contribution;
        if ($this->include_tax_relief && $this->tax_relief_rate > 0) {
            $this->tax_relief_amount = ($this->monthly_contribution * ($this->tax_relief_rate / 100)) * $months;
            // Effective contribution is higher due to tax relief
            $effective_monthly_contribution = $this->monthly_contribution / (1 - ($this->tax_relief_rate / 100));
        }

        // Future value of current balance
        $fv_current = $this->current_balance * pow(1 + ($this->expected_return / 100), $this->years_until_retirement);

        // Future value of monthly contributions
        if ($monthly_rate > 0) {
            $fv_contributions = $effective_monthly_contribution * ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        } else {
            $fv_contributions = $effective_monthly_contribution * $months;
        }

        $this->future_value = $fv_current + $fv_contributions;
        $this->total_contributions = $this->current_balance + ($effective_monthly_contribution * $months);
        $this->growth_amount = $this->future_value - $this->total_contributions;

        // Year-by-year breakdown
        $this->yearly_breakdown = [];
        $current_value = $this->current_balance;
        for ($year = 1; $year <= $this->years_until_retirement; $year++) {
            $year_contributions = $effective_monthly_contribution * 12;
            $current_value = $current_value * (1 + ($this->expected_return / 100)) + $year_contributions;
            $this->yearly_breakdown[] = [
                'year' => $year,
                'value' => $current_value,
                'contributions' => $this->current_balance + ($effective_monthly_contribution * 12 * $year),
            ];
        }

        $this->show_results = true;

        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'pension_growth',
                'input_data' => $this->only(['current_balance', 'monthly_contribution', 'years_until_retirement', 'expected_return', 'tax_relief_rate', 'include_tax_relief']),
                'result_data' => [
                    'future_value' => $this->future_value,
                    'total_contributions' => $this->total_contributions,
                    'growth_amount' => $this->growth_amount,
                    'tax_relief_amount' => $this->tax_relief_amount,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.pension-growth-calculator');
    }
}
