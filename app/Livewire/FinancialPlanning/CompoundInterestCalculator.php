<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CompoundInterestCalculator extends Component
{
    // Inputs
    public $starting_amount = 10000;
    public $monthly_addition = 500;
    public $years = 20;
    public $interest_rate = 7;

    // Results
    public $future_value = 0;
    public $total_contributions = 0;
    public $interest_earned = 0;
    public $yearly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'starting_amount' => 'required|numeric|min:0',
            'monthly_addition' => 'required|numeric|min:0',
            'years' => 'required|numeric|min:1|max:100',
            'interest_rate' => 'required|numeric|min:0|max:30',
        ]);

        $monthly_rate = ($this->interest_rate / 100) / 12;
        $months = $this->years * 12;

        // Future value of starting amount
        $fv_starting = $this->starting_amount * pow(1 + ($this->interest_rate / 100), $this->years);

        // Future value of monthly additions (annuity)
        if ($monthly_rate > 0) {
            $fv_monthly = $this->monthly_addition * ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate) * (1 + $monthly_rate);
        } else {
            $fv_monthly = $this->monthly_addition * $months;
        }

        $this->future_value = $fv_starting + $fv_monthly;
        $this->total_contributions = $this->starting_amount + ($this->monthly_addition * $months);
        $this->interest_earned = $this->future_value - $this->total_contributions;

        // Year-by-year breakdown
        $this->yearly_breakdown = [];
        $current_value = $this->starting_amount;
        for ($year = 1; $year <= $this->years; $year++) {
            $current_value = $current_value * (1 + ($this->interest_rate / 100)) + ($this->monthly_addition * 12);
            $this->yearly_breakdown[] = [
                'year' => $year,
                'value' => $current_value,
                'contributions' => $this->starting_amount + ($this->monthly_addition * 12 * $year),
                'interest' => $current_value - ($this->starting_amount + ($this->monthly_addition * 12 * $year)),
            ];
        }

        $this->show_results = true;

        // Save calculation if user is logged in
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'compound_interest',
                'input_data' => [
                    'starting_amount' => $this->starting_amount,
                    'monthly_addition' => $this->monthly_addition,
                    'years' => $this->years,
                    'interest_rate' => $this->interest_rate,
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
        return view('livewire.financial-planning.compound-interest-calculator');
    }
}
