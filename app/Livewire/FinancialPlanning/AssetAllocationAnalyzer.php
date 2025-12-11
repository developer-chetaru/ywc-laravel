<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class AssetAllocationAnalyzer extends Component
{
    // Inputs
    public $age = 30;
    public $risk_tolerance = 'moderate'; // conservative, moderate, aggressive
    public $time_horizon = 30;
    public $investment_amount = 100000;
    public $stocks_percent = 70;
    public $bonds_percent = 20;
    public $cash_percent = 10;
    public $real_estate_percent = 0;
    public $other_percent = 0;

    // Results
    public $recommended_allocation = [];
    public $current_allocation = [];
    public $risk_score = 0;
    public $expected_return = 0;
    public $recommendations = [];
    public $show_results = false;

    public function calculate()
    {
        // Normalize percentages
        $total = $this->stocks_percent + $this->bonds_percent + $this->cash_percent + 
                 $this->real_estate_percent + $this->other_percent;
        
        if ($total != 100) {
            $this->addError('allocation', 'Asset allocation must total 100%');
            return;
        }

        $this->validate([
            'age' => 'required|numeric|min:18|max:100',
            'risk_tolerance' => 'required|in:conservative,moderate,aggressive',
            'time_horizon' => 'required|numeric|min:1|max:100',
            'investment_amount' => 'required|numeric|min:0',
        ]);

        // Calculate recommended allocation based on age and risk tolerance
        $this->calculateRecommendedAllocation();
        
        // Current allocation
        $this->current_allocation = [
            'stocks' => $this->stocks_percent,
            'bonds' => $this->bonds_percent,
            'cash' => $this->cash_percent,
            'real_estate' => $this->real_estate_percent,
            'other' => $this->other_percent,
        ];

        // Calculate risk score (0-100)
        $this->risk_score = ($this->stocks_percent * 0.8) + ($this->bonds_percent * 0.4) + 
                           ($this->cash_percent * 0.1) + ($this->real_estate_percent * 0.6);

        // Expected return based on allocation
        $this->expected_return = ($this->stocks_percent * 0.10) + ($this->bonds_percent * 0.04) + 
                                 ($this->cash_percent * 0.02) + ($this->real_estate_percent * 0.07);

        // Generate recommendations
        $this->generateRecommendations();

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'asset_allocation',
                'input_data' => [
                    'age' => $this->age,
                    'risk_tolerance' => $this->risk_tolerance,
                    'time_horizon' => $this->time_horizon,
                    'investment_amount' => $this->investment_amount,
                    'current_allocation' => $this->current_allocation,
                ],
                'result_data' => [
                    'recommended_allocation' => $this->recommended_allocation,
                    'risk_score' => $this->risk_score,
                    'expected_return' => $this->expected_return,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    private function calculateRecommendedAllocation()
    {
        $age_based_stocks = max(20, 110 - $this->age);
        
        if ($this->risk_tolerance === 'conservative') {
            $this->recommended_allocation = [
                'stocks' => max(30, $age_based_stocks - 20),
                'bonds' => max(40, min(60, 100 - ($age_based_stocks - 20))),
                'cash' => 10,
                'real_estate' => 0,
                'other' => 0,
            ];
        } elseif ($this->risk_tolerance === 'moderate') {
            $this->recommended_allocation = [
                'stocks' => $age_based_stocks,
                'bonds' => min(40, max(20, 100 - $age_based_stocks - 10)),
                'cash' => 10,
                'real_estate' => 0,
                'other' => 0,
            ];
        } else { // aggressive
            $this->recommended_allocation = [
                'stocks' => min(90, $age_based_stocks + 10),
                'bonds' => max(0, min(20, 100 - ($age_based_stocks + 10))),
                'cash' => 5,
                'real_estate' => 0,
                'other' => 0,
            ];
        }
    }

    private function generateRecommendations()
    {
        $this->recommendations = [];

        $stock_diff = $this->stocks_percent - $this->recommended_allocation['stocks'];
        if (abs($stock_diff) > 5) {
            if ($stock_diff > 0) {
                $this->recommendations[] = "Consider reducing stocks by " . round(abs($stock_diff)) . "% - you may be taking more risk than recommended.";
            } else {
                $this->recommendations[] = "Consider increasing stocks by " . round(abs($stock_diff)) . "% - you may be missing growth opportunities.";
            }
        }

        if ($this->age < 40 && $this->bonds_percent > 30) {
            $this->recommendations[] = "For your age, consider reducing bond allocation to maximize growth.";
        }

        if ($this->time_horizon > 20 && $this->cash_percent > 15) {
            $this->recommendations[] = "With a long time horizon, consider reducing cash holdings to earn higher returns.";
        }

        if (empty($this->recommendations)) {
            $this->recommendations[] = "Your asset allocation looks well-balanced for your age and risk tolerance!";
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.asset-allocation-analyzer');
    }
}
