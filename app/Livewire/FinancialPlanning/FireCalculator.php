<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class FireCalculator extends Component
{
    // Inputs
    public $current_age = 30;
    public $current_savings = 50000;
    public $annual_income = 80000;
    public $annual_expenses = 40000;
    public $savings_rate = 0;
    public $expected_return = 7;
    public $fire_target = 2500000; // 25x annual expenses (4% rule)

    // Results
    public $years_to_fire = 0;
    public $fire_age = 0;
    public $total_needed = 0;
    public $monthly_savings = 0;
    public $projected_net_worth = [];
    public $show_results = false;

    public function updatedAnnualIncome()
    {
        $this->calculateSavingsRate();
    }

    public function updatedAnnualExpenses()
    {
        $this->calculateSavingsRate();
    }

    public function calculateSavingsRate()
    {
        if ($this->annual_income > 0) {
            $this->savings_rate = (($this->annual_income - $this->annual_expenses) / $this->annual_income) * 100;
            $this->monthly_savings = ($this->annual_income - $this->annual_expenses) / 12;
            $this->fire_target = $this->annual_expenses * 25; // 4% rule
        }
    }

    public function calculate()
    {
        $this->validate([
            'current_age' => 'required|numeric|min:18|max:100',
            'current_savings' => 'required|numeric|min:0',
            'annual_income' => 'required|numeric|min:0',
            'annual_expenses' => 'required|numeric|min:0|lte:annual_income',
            'expected_return' => 'required|numeric|min:0|max:20',
        ]);

        $this->calculateSavingsRate();

        $savings_per_year = $this->annual_income - $this->annual_expenses;
        $this->total_needed = $this->fire_target;

        // Calculate years to FIRE using iteration
        $current_balance = $this->current_savings;
        $this->years_to_fire = 0;
        $this->projected_net_worth = [];

        while ($current_balance < $this->total_needed && $this->years_to_fire < 100) {
            // Add annual savings
            $current_balance += $savings_per_year;
            
            // Apply investment return
            $current_balance *= (1 + ($this->expected_return / 100));
            
            $this->years_to_fire++;
            $this->projected_net_worth[] = [
                'year' => $this->years_to_fire,
                'age' => $this->current_age + $this->years_to_fire,
                'net_worth' => round($current_balance, 2),
                'savings' => round($savings_per_year * $this->years_to_fire, 2),
                'growth' => round($current_balance - $this->current_savings - ($savings_per_year * $this->years_to_fire), 2),
            ];
        }

        $this->fire_age = $this->current_age + $this->years_to_fire;
        $this->show_results = true;

        // Save calculation if user is logged in
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'fire',
                'input_data' => [
                    'current_age' => $this->current_age,
                    'current_savings' => $this->current_savings,
                    'annual_income' => $this->annual_income,
                    'annual_expenses' => $this->annual_expenses,
                    'savings_rate' => $this->savings_rate,
                    'expected_return' => $this->expected_return,
                    'fire_target' => $this->fire_target,
                ],
                'result_data' => [
                    'years_to_fire' => $this->years_to_fire,
                    'fire_age' => $this->fire_age,
                    'total_needed' => $this->total_needed,
                    'monthly_savings' => $this->monthly_savings,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.fire-calculator');
    }
}
