<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class InvestmentReturnProjector extends Component
{
    // Inputs
    public $initial_investment = 10000;
    public $monthly_contribution = 500;
    public $years = 20;
    public $expected_return = 7;
    public $contribution_increase = 0; // Annual increase in contributions (%)

    // Results
    public $future_value = 0;
    public $total_contributions = 0;
    public $interest_earned = 0;
    public $yearly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'initial_investment' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            'years' => 'required|numeric|min:1|max:100',
            'expected_return' => 'required|numeric|min:0|max:20',
            'contribution_increase' => 'required|numeric|min:0|max:50',
        ]);

        $monthly_rate = ($this->expected_return / 100) / 12;
        $months = $this->years * 12;
        $current_balance = $this->initial_investment;
        $this->yearly_breakdown = [];
        $total_contributed = $this->initial_investment;
        $current_monthly_contribution = $this->monthly_contribution;

        // Calculate year by year
        for ($year = 1; $year <= $this->years; $year++) {
            $year_start_balance = $current_balance;
            $year_contributions = 0;

            // Process 12 months
            for ($month = 1; $month <= 12; $month++) {
                // Add monthly contribution
                $current_balance += $current_monthly_contribution;
                $year_contributions += $current_monthly_contribution;
                $total_contributed += $current_monthly_contribution;

                // Apply monthly return
                $current_balance *= (1 + $monthly_rate);
            }

            // Increase contribution for next year
            if ($this->contribution_increase > 0) {
                $current_monthly_contribution *= (1 + ($this->contribution_increase / 100));
            }

            $year_end_balance = $current_balance;
            $year_growth = $year_end_balance - $year_start_balance - $year_contributions;

            $this->yearly_breakdown[] = [
                'year' => $year,
                'start_balance' => round($year_start_balance, 2),
                'contributions' => round($year_contributions, 2),
                'growth' => round($year_growth, 2),
                'end_balance' => round($year_end_balance, 2),
            ];
        }

        $this->future_value = round($current_balance, 2);
        $this->total_contributions = round($total_contributed, 2);
        $this->interest_earned = round($this->future_value - $this->total_contributions, 2);
        $this->show_results = true;

        // Save calculation if user is logged in
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'investment_return',
                'input_data' => [
                    'initial_investment' => $this->initial_investment,
                    'monthly_contribution' => $this->monthly_contribution,
                    'years' => $this->years,
                    'expected_return' => $this->expected_return,
                    'contribution_increase' => $this->contribution_increase,
                ],
                'result_data' => [
                    'future_value' => $this->future_value,
                    'total_contributions' => $this->total_contributions,
                    'interest_earned' => $this->interest_earned,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.investment-return-projector');
    }
}
