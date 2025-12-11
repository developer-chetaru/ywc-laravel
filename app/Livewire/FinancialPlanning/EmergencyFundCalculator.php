<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class EmergencyFundCalculator extends Component
{
    public $monthly_expenses = 3000;
    public $job_security = 'stable'; // stable, moderate, unstable
    public $dependents = 0;
    public $current_emergency_fund = 0;
    public $monthly_savings = 500;

    public $recommended_months = 6;
    public $recommended_amount = 0;
    public $months_to_build = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'monthly_expenses' => 'required|numeric|min:0',
            'job_security' => 'required|in:stable,moderate,unstable',
            'dependents' => 'required|numeric|min:0|max:10',
            'current_emergency_fund' => 'required|numeric|min:0',
            'monthly_savings' => 'required|numeric|min:0',
        ]);

        // Determine recommended months based on job security and dependents
        $base_months = 3;
        if ($this->job_security === 'moderate') $base_months = 6;
        if ($this->job_security === 'unstable') $base_months = 12;
        if ($this->dependents > 0) $base_months += ceil($this->dependents / 2);

        $this->recommended_months = min(12, $base_months);
        $this->recommended_amount = $this->monthly_expenses * $this->recommended_months;

        // Calculate months to build fund
        $needed = max(0, $this->recommended_amount - $this->current_emergency_fund);
        if ($this->monthly_savings > 0) {
            $this->months_to_build = ceil($needed / $this->monthly_savings);
        } else {
            $this->months_to_build = 999; // Infinite if no savings
        }

        $this->show_results = true;

        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'emergency_fund',
                'input_data' => $this->only(['monthly_expenses', 'job_security', 'dependents', 'current_emergency_fund', 'monthly_savings']),
                'result_data' => [
                    'recommended_months' => $this->recommended_months,
                    'recommended_amount' => $this->recommended_amount,
                    'months_to_build' => $this->months_to_build,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.emergency-fund-calculator');
    }
}
