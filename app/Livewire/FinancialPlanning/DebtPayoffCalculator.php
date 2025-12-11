<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class DebtPayoffCalculator extends Component
{
    // Inputs
    public $debt_amount = 10000;
    public $interest_rate = 18;
    public $monthly_payment = 300;
    public $strategy = 'minimum'; // minimum, avalanche, snowball

    // Results
    public $months_to_payoff = 0;
    public $total_interest = 0;
    public $total_paid = 0;
    public $payment_schedule = [];
    public $recommended_payment = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'debt_amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'monthly_payment' => 'required|numeric|min:0',
            'strategy' => 'required|in:minimum,avalanche,snowball',
        ]);

        // Calculate minimum payment (2% of balance or interest + 1% of principal)
        $monthly_rate = ($this->interest_rate / 100) / 12;
        $interest_only = $this->debt_amount * $monthly_rate;
        $this->recommended_payment = max($interest_only * 1.5, $this->debt_amount * 0.02);

        if ($this->monthly_payment <= $interest_only) {
            $this->addError('monthly_payment', 'Monthly payment must be greater than interest accrued to pay off debt.');
            return;
        }

        // Calculate payoff schedule
        $balance = $this->debt_amount;
        $month = 0;
        $total_interest_paid = 0;
        $this->payment_schedule = [];

        while ($balance > 0.01 && $month < 600) { // Max 50 years
            $month++;
            $interest_payment = $balance * $monthly_rate;
            $principal_payment = $this->monthly_payment - $interest_payment;

            if ($principal_payment > $balance) {
                $principal_payment = $balance;
                $this->monthly_payment = $principal_payment + $interest_payment;
            }

            $balance -= $principal_payment;
            $total_interest_paid += $interest_payment;

            if ($month % 6 == 0 || $balance <= 0.01) { // Every 6 months or final payment
                $this->payment_schedule[] = [
                    'month' => $month,
                    'balance' => round(max(0, $balance), 2),
                    'total_interest' => round($total_interest_paid, 2),
                    'principal_paid' => round($this->debt_amount - max(0, $balance), 2),
                ];
            }

            if ($balance <= 0.01) {
                break;
            }
        }

        $this->months_to_payoff = $month;
        $this->total_interest = round($total_interest_paid, 2);
        $this->total_paid = round($this->debt_amount + $this->total_interest, 2);
        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'debt_payoff',
                'input_data' => [
                    'debt_amount' => $this->debt_amount,
                    'interest_rate' => $this->interest_rate,
                    'monthly_payment' => $this->monthly_payment,
                    'strategy' => $this->strategy,
                ],
                'result_data' => [
                    'months_to_payoff' => $this->months_to_payoff,
                    'total_interest' => $this->total_interest,
                    'total_paid' => $this->total_paid,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.debt-payoff-calculator');
    }
}
