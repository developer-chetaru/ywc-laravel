<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class IncomeTaxEstimator extends Component
{
    // Inputs
    public $annual_income = 80000;
    public $filing_status = 'single'; // single, married_joint, married_separate, head_of_household
    public $tax_deductions = 12500; // Standard or itemized
    public $tax_credits = 0;
    public $withholdings = 8000;
    public $country = 'US'; // US, UK, EU

    // Results
    public $taxable_income = 0;
    public $tax_before_credits = 0;
    public $tax_after_credits = 0;
    public $effective_tax_rate = 0;
    public $marginal_tax_rate = 0;
    public $refund_or_owe = 0;
    public $tax_brackets_used = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'annual_income' => 'required|numeric|min:0',
            'filing_status' => 'required|in:single,married_joint,married_separate,head_of_household',
            'tax_deductions' => 'required|numeric|min:0',
            'tax_credits' => 'required|numeric|min:0',
            'withholdings' => 'required|numeric|min:0',
        ]);

        // Calculate taxable income
        $this->taxable_income = max(0, $this->annual_income - $this->tax_deductions);

        // US Tax Brackets 2024 (simplified)
        if ($this->country === 'US') {
            $brackets = $this->getUSTaxBrackets($this->filing_status);
            $this->calculateTaxFromBrackets($brackets);
        } else {
            // Simplified progressive tax calculation
            $this->calculateProgressiveTax();
        }

        // Apply credits
        $this->tax_after_credits = max(0, $this->tax_before_credits - $this->tax_credits);

        // Calculate rates
        $this->effective_tax_rate = $this->annual_income > 0 ? 
            ($this->tax_after_credits / $this->annual_income) * 100 : 0;

        // Refund or amount owed
        $this->refund_or_owe = $this->withholdings - $this->tax_after_credits;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'income_tax',
                'input_data' => [
                    'annual_income' => $this->annual_income,
                    'filing_status' => $this->filing_status,
                    'tax_deductions' => $this->tax_deductions,
                    'tax_credits' => $this->tax_credits,
                ],
                'result_data' => [
                    'taxable_income' => $this->taxable_income,
                    'tax_after_credits' => $this->tax_after_credits,
                    'effective_tax_rate' => $this->effective_tax_rate,
                    'marginal_tax_rate' => $this->marginal_tax_rate,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    private function getUSTaxBrackets($status)
    {
        // 2024 US Tax Brackets (simplified)
        $brackets = [
            'single' => [
                [0, 11600, 0.10],
                [11600, 47150, 0.12],
                [47150, 100525, 0.22],
                [100525, 191950, 0.24],
                [191950, 243725, 0.32],
                [243725, 609350, 0.35],
                [609350, PHP_INT_MAX, 0.37],
            ],
            'married_joint' => [
                [0, 23200, 0.10],
                [23200, 94300, 0.12],
                [94300, 201050, 0.22],
                [201050, 383900, 0.24],
                [383900, 487450, 0.32],
                [487450, 731200, 0.35],
                [731200, PHP_INT_MAX, 0.37],
            ],
        ];

        return $brackets[$status] ?? $brackets['single'];
    }

    private function calculateTaxFromBrackets($brackets)
    {
        $remaining_income = $this->taxable_income;
        $total_tax = 0;
        $this->tax_brackets_used = [];

        foreach ($brackets as $bracket) {
            [$min, $max, $rate] = $bracket;
            
            if ($remaining_income <= 0) break;

            $taxable_in_bracket = min($remaining_income, $max - $min);
            $tax_in_bracket = $taxable_in_bracket * $rate;
            $total_tax += $tax_in_bracket;
            $remaining_income -= $taxable_in_bracket;

            if ($tax_in_bracket > 0) {
                $this->tax_brackets_used[] = [
                    'bracket' => '$' . number_format($min) . ' - $' . number_format($max),
                    'taxable' => $taxable_in_bracket,
                    'rate' => $rate * 100,
                    'tax' => $tax_in_bracket,
                ];
            }

            if ($remaining_income > 0 && $remaining_income < ($max - $min)) {
                $this->marginal_tax_rate = $rate * 100;
            }
        }

        $this->tax_before_credits = $total_tax;
        
        if (empty($this->tax_brackets_used) && $this->taxable_income > 0) {
            $this->marginal_tax_rate = $brackets[0][2] * 100;
        }
    }

    private function calculateProgressiveTax()
    {
        // Simplified progressive tax calculation
        if ($this->taxable_income <= 20000) {
            $this->tax_before_credits = $this->taxable_income * 0.10;
            $this->marginal_tax_rate = 10;
        } elseif ($this->taxable_income <= 50000) {
            $this->tax_before_credits = 2000 + (($this->taxable_income - 20000) * 0.20);
            $this->marginal_tax_rate = 20;
        } elseif ($this->taxable_income <= 100000) {
            $this->tax_before_credits = 8000 + (($this->taxable_income - 50000) * 0.30);
            $this->marginal_tax_rate = 30;
        } else {
            $this->tax_before_credits = 23000 + (($this->taxable_income - 100000) * 0.40);
            $this->marginal_tax_rate = 40;
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.income-tax-estimator');
    }
}
