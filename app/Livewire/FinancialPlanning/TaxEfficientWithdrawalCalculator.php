<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class TaxEfficientWithdrawalCalculator extends Component
{
    // Inputs
    public $annual_need = 60000;
    public $taxable_account_balance = 200000;
    public $tax_deferred_balance = 300000; // 401k, IRA
    public $tax_free_balance = 100000; // Roth IRA
    public $taxable_gains_percent = 50; // % of taxable account that is gains
    public $current_age = 65;
    public $years = 25;
    public $long_term_cap_gains_rate = 15; // %
    public $ordinary_income_rate = 22; // %

    // Results
    public $withdrawal_strategy = [];
    public $total_tax_paid = 0;
    public $taxable_annual_withdrawal = 0;
    public $tax_deferred_annual_withdrawal = 0;
    public $tax_free_annual_withdrawal = 0;
    public $strategy_recommendation = '';
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'annual_need' => 'required|numeric|min:0',
            'taxable_account_balance' => 'required|numeric|min:0',
            'tax_deferred_balance' => 'required|numeric|min:0',
            'tax_free_balance' => 'required|numeric|min:0',
            'taxable_gains_percent' => 'required|numeric|min:0|max:100',
            'current_age' => 'required|numeric|min:59|max:100',
            'years' => 'required|numeric|min:1|max:50',
            'long_term_cap_gains_rate' => 'required|numeric|min:0|max:50',
            'ordinary_income_rate' => 'required|numeric|min:0|max:50',
        ]);

        $total_balance = $this->taxable_account_balance + $this->tax_deferred_balance + $this->tax_free_balance;
        
        // Strategy 1: Taxable first (recommended for most)
        $taxable_first = $this->calculateWithdrawalStrategy('taxable_first');
        
        // Strategy 2: Tax-deferred first
        $tax_deferred_first = $this->calculateWithdrawalStrategy('tax_deferred_first');
        
        // Strategy 3: Balanced approach
        $balanced = $this->calculateWithdrawalStrategy('balanced');

        // Find best strategy
        $strategies = [
            'Taxable First (Recommended)' => $taxable_first,
            'Tax-Deferred First' => $tax_deferred_first,
            'Balanced Approach' => $balanced,
        ];

        $best_strategy = collect($strategies)->sortBy('total_tax')->first();
        $this->strategy_recommendation = array_search($best_strategy, $strategies);
        
        // Use best strategy for display
        $this->taxable_annual_withdrawal = $best_strategy['taxable'];
        $this->tax_deferred_annual_withdrawal = $best_strategy['tax_deferred'];
        $this->tax_free_annual_withdrawal = $best_strategy['tax_free'];
        $this->total_tax_paid = $best_strategy['total_tax'];
        $this->withdrawal_strategy = $strategies;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'tax_efficient_withdrawal',
                'input_data' => [
                    'annual_need' => $this->annual_need,
                    'total_balance' => $total_balance,
                    'years' => $this->years,
                ],
                'result_data' => [
                    'recommended_strategy' => $this->strategy_recommendation,
                    'total_tax_paid' => $this->total_tax_paid,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    private function calculateWithdrawalStrategy($strategy_type)
    {
        $remaining_need = $this->annual_need;
        $total_tax = 0;
        $taxable_withdrawn = 0;
        $tax_deferred_withdrawn = 0;
        $tax_free_withdrawn = 0;

        if ($strategy_type === 'taxable_first') {
            // Withdraw from taxable first (up to need)
            $taxable_amount = min($remaining_need, $this->taxable_account_balance);
            $taxable_withdrawn = $taxable_amount;
            $gains_withdrawn = $taxable_amount * ($this->taxable_gains_percent / 100);
            $total_tax += $gains_withdrawn * ($this->long_term_cap_gains_rate / 100);
            $remaining_need -= $taxable_amount;

            // Then tax-deferred
            if ($remaining_need > 0) {
                $tax_deferred_amount = min($remaining_need, $this->tax_deferred_balance);
                $tax_deferred_withdrawn = $tax_deferred_amount;
                $total_tax += $tax_deferred_amount * ($this->ordinary_income_rate / 100);
                $remaining_need -= $tax_deferred_amount;
            }

            // Finally tax-free
            if ($remaining_need > 0) {
                $tax_free_withdrawn = min($remaining_need, $this->tax_free_balance);
            }

        } elseif ($strategy_type === 'tax_deferred_first') {
            // Withdraw from tax-deferred first
            $tax_deferred_amount = min($remaining_need, $this->tax_deferred_balance);
            $tax_deferred_withdrawn = $tax_deferred_amount;
            $total_tax += $tax_deferred_amount * ($this->ordinary_income_rate / 100);
            $remaining_need -= $tax_deferred_amount;

            // Then taxable
            if ($remaining_need > 0) {
                $taxable_amount = min($remaining_need, $this->taxable_account_balance);
                $taxable_withdrawn = $taxable_amount;
                $gains_withdrawn = $taxable_amount * ($this->taxable_gains_percent / 100);
                $total_tax += $gains_withdrawn * ($this->long_term_cap_gains_rate / 100);
                $remaining_need -= $taxable_amount;
            }

            // Finally tax-free
            if ($remaining_need > 0) {
                $tax_free_withdrawn = min($remaining_need, $this->tax_free_balance);
            }

        } else { // balanced
            // Withdraw proportionally
            $total = $this->taxable_account_balance + $this->tax_deferred_balance + $this->tax_free_balance;
            
            $taxable_amount = ($this->taxable_account_balance / $total) * $this->annual_need;
            $taxable_withdrawn = min($taxable_amount, $this->taxable_account_balance);
            $gains_withdrawn = $taxable_withdrawn * ($this->taxable_gains_percent / 100);
            $total_tax += $gains_withdrawn * ($this->long_term_cap_gains_rate / 100);

            $tax_deferred_amount = ($this->tax_deferred_balance / $total) * $this->annual_need;
            $tax_deferred_withdrawn = min($tax_deferred_amount, $this->tax_deferred_balance);
            $total_tax += $tax_deferred_withdrawn * ($this->ordinary_income_rate / 100);

            $tax_free_amount = ($this->tax_free_balance / $total) * $this->annual_need;
            $tax_free_withdrawn = min($tax_free_amount, $this->tax_free_balance);
        }

        return [
            'taxable' => $taxable_withdrawn,
            'tax_deferred' => $tax_deferred_withdrawn,
            'tax_free' => $tax_free_withdrawn,
            'total_tax' => $total_tax,
        ];
    }

    public function render()
    {
        return view('livewire.financial-planning.tax-efficient-withdrawal-calculator');
    }
}
