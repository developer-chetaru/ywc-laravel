<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PortfolioRiskCalculator extends Component
{
    // Inputs
    public $portfolio_value = 100000;
    public $stocks_percent = 70;
    public $bonds_percent = 20;
    public $cash_percent = 5;
    public $real_estate_percent = 5;
    public $other_percent = 0;
    public $age = 35;
    public $time_horizon = 25;

    // Results
    public $risk_score = 0;
    public $risk_level = ''; // low, moderate, high, very_high
    public $max_loss_potential = 0;
    public $expected_return = 0;
    public $volatility_estimate = 0;
    public $recommendations = [];
    public $show_results = false;

    public function calculate()
    {
        // Normalize percentages
        $total = $this->stocks_percent + $this->bonds_percent + $this->cash_percent + 
                 $this->real_estate_percent + $this->other_percent;
        
        if (abs($total - 100) > 0.01) {
            $this->addError('allocation', 'Portfolio allocation must total 100%');
            return;
        }

        $this->validate([
            'portfolio_value' => 'required|numeric|min:0',
            'age' => 'required|numeric|min:18|max:100',
            'time_horizon' => 'required|numeric|min:1|max:100',
        ]);

        // Risk weights for each asset class
        $risk_weights = [
            'stocks' => 0.85,
            'bonds' => 0.30,
            'cash' => 0.05,
            'real_estate' => 0.50,
            'other' => 0.60,
        ];

        // Calculate weighted risk score (0-100)
        $this->risk_score = 
            ($this->stocks_percent * $risk_weights['stocks']) +
            ($this->bonds_percent * $risk_weights['bonds']) +
            ($this->cash_percent * $risk_weights['cash']) +
            ($this->real_estate_percent * $risk_weights['real_estate']) +
            ($this->other_percent * $risk_weights['other']);

        // Determine risk level
        if ($this->risk_score < 30) {
            $this->risk_level = 'low';
        } elseif ($this->risk_score < 50) {
            $this->risk_level = 'moderate';
        } elseif ($this->risk_score < 70) {
            $this->risk_level = 'high';
        } else {
            $this->risk_level = 'very_high';
        }

        // Estimate maximum potential loss (worst-case scenario)
        $this->max_loss_potential = $this->portfolio_value * ($this->risk_score / 100) * 0.5; // 50% of risk score as max loss

        // Expected return (weighted)
        $expected_returns = [
            'stocks' => 10,
            'bonds' => 4,
            'cash' => 2,
            'real_estate' => 7,
            'other' => 6,
        ];

        $this->expected_return = 
            ($this->stocks_percent * $expected_returns['stocks']) +
            ($this->bonds_percent * $expected_returns['bonds']) +
            ($this->cash_percent * $expected_returns['cash']) +
            ($this->real_estate_percent * $expected_returns['real_estate']) +
            ($this->other_percent * $expected_returns['other']);

        // Volatility estimate (annual standard deviation approximation)
        $this->volatility_estimate = $this->risk_score * 0.8; // Simplified estimate

        // Generate recommendations
        $this->generateRecommendations();

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'portfolio_risk',
                'input_data' => [
                    'portfolio_value' => $this->portfolio_value,
                    'allocation' => [
                        'stocks' => $this->stocks_percent,
                        'bonds' => $this->bonds_percent,
                        'cash' => $this->cash_percent,
                        'real_estate' => $this->real_estate_percent,
                        'other' => $this->other_percent,
                    ],
                    'age' => $this->age,
                    'time_horizon' => $this->time_horizon,
                ],
                'result_data' => [
                    'risk_score' => $this->risk_score,
                    'risk_level' => $this->risk_level,
                    'expected_return' => $this->expected_return,
                    'max_loss_potential' => $this->max_loss_potential,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    private function generateRecommendations()
    {
        $this->recommendations = [];

        if ($this->age < 40 && $this->risk_score < 50) {
            $this->recommendations[] = "You're young with a long time horizon - consider increasing stock allocation for better growth.";
        } elseif ($this->age > 55 && $this->risk_score > 60) {
            $this->recommendations[] = "You're closer to retirement - consider reducing risk by increasing bonds/cash allocation.";
        }

        if ($this->time_horizon < 5 && $this->risk_score > 50) {
            $this->recommendations[] = "Short time horizon - reduce risk to protect capital from market volatility.";
        }

        if ($this->stocks_percent > 90) {
            $this->recommendations[] = "Very high stock concentration increases risk - consider diversifying with bonds or real estate.";
        }

        if ($this->cash_percent > 20 && $this->time_horizon > 10) {
            $this->recommendations[] = "High cash allocation may reduce returns - consider investing in growth assets for long-term goals.";
        }

        if ($this->risk_score > 70) {
            $this->recommendations[] = "Very high risk portfolio - ensure you can withstand potential losses of 30-50% in market downturns.";
        } elseif ($this->risk_score < 30 && $this->time_horizon > 15) {
            $this->recommendations[] = "Very conservative portfolio may not keep up with inflation - consider adding growth assets.";
        }

        if (empty($this->recommendations)) {
            $this->recommendations[] = "Your portfolio risk level appears appropriate for your age and time horizon.";
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.portfolio-risk-calculator');
    }
}
