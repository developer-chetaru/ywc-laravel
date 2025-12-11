<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class DividendIncomeCalculator extends Component
{
    // Inputs
    public $portfolio_value = 100000;
    public $dividend_yield = 3; // Annual %
    public $dividend_growth_rate = 5; // Annual %
    public $years = 20;
    public $reinvest_dividends = true;
    public $monthly_addition = 0;

    // Results
    public $annual_dividend_income = 0;
    public $monthly_dividend_income = 0;
    public $final_portfolio_value = 0;
    public $total_dividends_received = 0;
    public $yearly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'portfolio_value' => 'required|numeric|min:0',
            'dividend_yield' => 'required|numeric|min:0|max:20',
            'dividend_growth_rate' => 'required|numeric|min:0|max:20',
            'years' => 'required|numeric|min:1|max:100',
            'monthly_addition' => 'required|numeric|min:0',
        ]);

        $current_portfolio = $this->portfolio_value;
        $current_yield = $this->dividend_yield / 100;
        $total_dividends = 0;
        $this->yearly_breakdown = [];

        // Calculate year by year
        for ($year = 1; $year <= $this->years; $year++) {
            // Annual dividend income
            $annual_dividend = $current_portfolio * $current_yield;
            $total_dividends += $annual_dividend;

            if ($this->reinvest_dividends) {
                // Reinvest dividends back into portfolio
                $current_portfolio += $annual_dividend;
            }

            // Add monthly contributions
            $current_portfolio += ($this->monthly_addition * 12);

            // Grow dividend yield with growth rate
            $current_yield *= (1 + ($this->dividend_growth_rate / 100));

            // Track yearly data
            $this->yearly_breakdown[] = [
                'year' => $year,
                'portfolio_value' => round($current_portfolio, 2),
                'annual_dividend' => round($annual_dividend, 2),
                'yield' => round($current_yield * 100, 2),
                'cumulative_dividends' => round($total_dividends, 2),
            ];
        }

        $this->final_portfolio_value = round($current_portfolio, 2);
        $this->total_dividends_received = round($total_dividends, 2);
        
        // Calculate current year dividend income
        $this->annual_dividend_income = round($current_portfolio * $current_yield, 2);
        $this->monthly_dividend_income = round($this->annual_dividend_income / 12, 2);

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'dividend_income',
                'input_data' => [
                    'portfolio_value' => $this->portfolio_value,
                    'dividend_yield' => $this->dividend_yield,
                    'dividend_growth_rate' => $this->dividend_growth_rate,
                    'years' => $this->years,
                    'reinvest_dividends' => $this->reinvest_dividends,
                    'monthly_addition' => $this->monthly_addition,
                ],
                'result_data' => [
                    'final_portfolio_value' => $this->final_portfolio_value,
                    'annual_dividend_income' => $this->annual_dividend_income,
                    'total_dividends_received' => $this->total_dividends_received,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.dividend-income-calculator');
    }
}
