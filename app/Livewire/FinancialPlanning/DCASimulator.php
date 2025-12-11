<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class DCASimulator extends Component
{
    // Inputs
    public $lump_sum_amount = 50000;
    public $dca_amount = 5000;
    public $monthly_investment = 500;
    public $investment_period_months = 60;
    public $annual_return = 7;
    public $strategy = 'both'; // lump_sum, dca, both

    // Results
    public $lump_sum_final_value = 0;
    public $dca_final_value = 0;
    public $difference = 0;
    public $lump_sum_gain = 0;
    public $dca_gain = 0;
    public $monthly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'lump_sum_amount' => 'required|numeric|min:0',
            'dca_amount' => 'required|numeric|min:0',
            'monthly_investment' => 'required|numeric|min:0',
            'investment_period_months' => 'required|numeric|min:1|max:600',
            'annual_return' => 'required|numeric|min:0|max:30',
            'strategy' => 'required|in:lump_sum,dca,both',
        ]);

        $monthly_return = ($this->annual_return / 100) / 12;

        // Lump Sum Strategy
        if ($this->strategy === 'lump_sum' || $this->strategy === 'both') {
            $this->lump_sum_final_value = $this->lump_sum_amount * pow(1 + $monthly_return, $this->investment_period_months);
            $this->lump_sum_gain = $this->lump_sum_final_value - $this->lump_sum_amount;
        }

        // Dollar-Cost Averaging Strategy
        if ($this->strategy === 'dca' || $this->strategy === 'both') {
            // Initial investment
            $dca_balance = $this->dca_amount;
            
            // Monthly investments with compounding
            for ($month = 1; $month <= $this->investment_period_months; $month++) {
                // Apply monthly return
                $dca_balance *= (1 + $monthly_return);
                
                // Add monthly investment
                $dca_balance += $this->monthly_investment;
                
                // Track every 6 months
                if ($month % 6 == 0 || $month == 1) {
                    $total_invested = $this->dca_amount + ($this->monthly_investment * $month);
                    $this->monthly_breakdown[] = [
                        'month' => $month,
                        'balance' => round($dca_balance, 2),
                        'total_invested' => round($total_invested, 2),
                        'gain' => round($dca_balance - $total_invested, 2),
                    ];
                }
            }
            
            $this->dca_final_value = $dca_balance;
            $total_dca_invested = $this->dca_amount + ($this->monthly_investment * $this->investment_period_months);
            $this->dca_gain = $this->dca_final_value - $total_dca_invested;
        }

        // Calculate difference
        if ($this->strategy === 'both') {
            $this->difference = $this->lump_sum_final_value - $this->dca_final_value;
        }

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'dca_simulator',
                'input_data' => [
                    'lump_sum_amount' => $this->lump_sum_amount,
                    'dca_amount' => $this->dca_amount,
                    'monthly_investment' => $this->monthly_investment,
                    'investment_period_months' => $this->investment_period_months,
                    'annual_return' => $this->annual_return,
                    'strategy' => $this->strategy,
                ],
                'result_data' => [
                    'lump_sum_final_value' => $this->lump_sum_final_value,
                    'dca_final_value' => $this->dca_final_value,
                    'difference' => $this->difference,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.d-c-a-simulator');
    }
}
