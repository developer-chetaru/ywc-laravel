<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CapitalGainsTaxCalculator extends Component
{
    // Inputs
    public $purchase_price = 100000;
    public $sale_price = 150000;
    public $selling_expenses = 5000;
    public $improvement_costs = 10000;
    public $holding_period_months = 25; // Long-term if 12+ months
    public $filing_status = 'single';
    public $taxable_income = 50000;
    public $long_term_rate_0 = 0;
    public $long_term_rate_15 = 15;
    public $long_term_rate_20 = 20;

    // Results
    public $cost_basis = 0;
    public $capital_gain = 0;
    public $taxable_gain = 0;
    public $capital_gains_tax = 0;
    public $net_profit = 0;
    public $is_long_term = false;
    public $tax_rate_used = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'selling_expenses' => 'required|numeric|min:0',
            'improvement_costs' => 'required|numeric|min:0',
            'holding_period_months' => 'required|numeric|min:0',
            'filing_status' => 'required|in:single,married_joint,married_separate,head_of_household',
            'taxable_income' => 'required|numeric|min:0',
        ]);

        // Calculate cost basis
        $this->cost_basis = $this->purchase_price + $this->improvement_costs + $this->selling_expenses;

        // Calculate capital gain
        $this->capital_gain = $this->sale_price - $this->cost_basis;
        $this->taxable_gain = max(0, $this->capital_gain);

        // Determine if long-term (12+ months)
        $this->is_long_term = $this->holding_period_months >= 12;

        if ($this->is_long_term) {
            // Long-term capital gains tax rates (2024 US brackets - simplified)
            $tax_brackets = $this->getLongTermCapitalGainsBrackets($this->filing_status);
            $total_income = $this->taxable_income + $this->taxable_gain;

            // Determine applicable rate
            foreach ($tax_brackets as $bracket) {
                [$threshold, $rate] = $bracket;
                if ($total_income >= $threshold) {
                    $this->tax_rate_used = $rate;
                }
            }

            // Progressive calculation
            $remaining_gain = $this->taxable_gain;
            $tax = 0;
            $current_threshold = 0;

            foreach ($tax_brackets as $bracket) {
                [$threshold, $rate] = $bracket;
                if ($remaining_gain <= 0) break;

                // Amount in this bracket
                $bracket_start = max($threshold, $this->taxable_income);
                $bracket_end = $threshold;

                if ($total_income > $bracket_start) {
                    $gain_in_bracket = min($remaining_gain, max(0, $total_income - $bracket_start));
                    $tax += $gain_in_bracket * ($rate / 100);
                    $remaining_gain -= $gain_in_bracket;
                }
            }

            $this->capital_gains_tax = $tax;

        } else {
            // Short-term capital gains taxed as ordinary income
            $this->tax_rate_used = $this->getOrdinaryIncomeRate($this->taxable_income + $this->taxable_gain);
            $this->capital_gains_tax = $this->taxable_gain * ($this->tax_rate_used / 100);
        }

        // Net profit after tax
        $this->net_profit = $this->capital_gain - $this->capital_gains_tax;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'capital_gains_tax',
                'input_data' => [
                    'purchase_price' => $this->purchase_price,
                    'sale_price' => $this->sale_price,
                    'capital_gain' => $this->capital_gain,
                    'holding_period_months' => $this->holding_period_months,
                ],
                'result_data' => [
                    'capital_gains_tax' => $this->capital_gains_tax,
                    'net_profit' => $this->net_profit,
                    'tax_rate_used' => $this->tax_rate_used,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    private function getLongTermCapitalGainsBrackets($status)
    {
        // 2024 Long-term capital gains brackets
        $brackets = [
            'single' => [
                [0, 0],
                [44625, 15],
                [492300, 20],
            ],
            'married_joint' => [
                [0, 0],
                [89250, 15],
                [553850, 20],
            ],
        ];

        return $brackets[$status] ?? $brackets['single'];
    }

    private function getOrdinaryIncomeRate($income)
    {
        // Simplified progressive rates
        if ($income <= 11600) return 10;
        if ($income <= 47150) return 12;
        if ($income <= 100525) return 22;
        if ($income <= 191950) return 24;
        if ($income <= 243725) return 32;
        if ($income <= 609350) return 35;
        return 37;
    }

    public function render()
    {
        return view('livewire.financial-planning.capital-gains-tax-calculator');
    }
}
