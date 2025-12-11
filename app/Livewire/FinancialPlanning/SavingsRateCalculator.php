<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class SavingsRateCalculator extends Component
{
    // Inputs
    public $annual_income = 80000;
    public $annual_expenses = 50000;
    public $savings_amount = 0;
    
    // Results
    public $savings_rate = 0;
    public $annual_savings = 0;
    public $monthly_savings = 0;
    public $years_to_fire = 0;
    public $fire_target = 0;
    public $recommendations = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'annual_income' => 'required|numeric|min:0',
            'annual_expenses' => 'required|numeric|min:0|lte:annual_income',
        ]);

        // Calculate savings
        $this->annual_savings = $this->annual_income - $this->annual_expenses;
        $this->monthly_savings = $this->annual_savings / 12;
        
        // Calculate savings rate
        $this->savings_rate = $this->annual_income > 0 ? 
            ($this->annual_savings / $this->annual_income) * 100 : 0;

        // FIRE calculation (4% rule)
        $this->fire_target = $this->annual_expenses * 25;
        
        // Estimate years to FIRE (simplified with 7% return assumption)
        if ($this->annual_savings > 0) {
            // Using logarithmic approximation
            $growth_rate = 0.07; // 7% annual return
            $current_savings = $this->savings_amount;
            $this->years_to_fire = 0;
            $balance = $current_savings;
            
            while ($balance < $this->fire_target && $this->years_to_fire < 100) {
                $balance += $this->annual_savings;
                $balance *= (1 + $growth_rate);
                $this->years_to_fire++;
            }
        }

        // Generate recommendations
        $this->generateRecommendations();

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'savings_rate',
                'input_data' => [
                    'annual_income' => $this->annual_income,
                    'annual_expenses' => $this->annual_expenses,
                    'savings_amount' => $this->savings_amount,
                ],
                'result_data' => [
                    'savings_rate' => $this->savings_rate,
                    'annual_savings' => $this->annual_savings,
                    'years_to_fire' => $this->years_to_fire,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    private function generateRecommendations()
    {
        $this->recommendations = [];

        if ($this->savings_rate < 10) {
            $this->recommendations[] = "Your savings rate is low. Aim to save at least 20% of income for financial security.";
        } elseif ($this->savings_rate < 20) {
            $this->recommendations[] = "Good start! Try to increase savings rate to 20-30% for better financial freedom.";
        } elseif ($this->savings_rate < 50) {
            $this->recommendations[] = "Excellent savings rate! You're on track for financial independence.";
        } else {
            $this->recommendations[] = "Outstanding! With a savings rate over 50%, you're achieving FIRE quickly!";
        }

        if ($this->savings_rate < 20) {
            $this->recommendations[] = "Consider: Reducing expenses by 10% can significantly boost your savings rate.";
            $this->recommendations[] = "Consider: Increasing income through side hustles or career advancement.";
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.savings-rate-calculator');
    }
}
