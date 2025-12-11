<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CostOfWaitingCalculator extends Component
{
    public $current_age = 25;
    public $monthly_contribution = 500;
    public $expected_return = 7;
    public $retirement_age = 65;

    public $scenarios = [];
    public $cost_of_waiting = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'current_age' => 'required|numeric|min:18|max:70',
            'monthly_contribution' => 'required|numeric|min:0',
            'expected_return' => 'required|numeric|min:0|max:20',
            'retirement_age' => 'required|numeric|min:' . ($this->current_age + 1) . '|max:80',
        ]);

        $years_to_retirement = $this->retirement_age - $this->current_age;
        $monthly_rate = ($this->expected_return / 100) / 12;

        $this->scenarios = [];
        $start_now_amount = 0;
        
        // Calculate scenario starting now
        $months = $years_to_retirement * 12;
        if ($monthly_rate > 0) {
            $start_now_amount = $this->monthly_contribution * ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
        } else {
            $start_now_amount = $this->monthly_contribution * $months;
        }
        
        $this->scenarios[] = [
            'age' => $this->current_age,
            'label' => 'Start Now',
            'amount' => $start_now_amount,
            'months_contributing' => $months,
        ];

        // Calculate scenarios for waiting 1, 3, 5 years
        foreach ([1, 3, 5] as $wait_years) {
            $start_age = $this->current_age + $wait_years;
            if ($start_age >= $this->retirement_age) continue;
            
            $months = ($years_to_retirement - $wait_years) * 12;
            $amount = 0;
            if ($monthly_rate > 0) {
                $amount = $this->monthly_contribution * ((pow(1 + $monthly_rate, $months) - 1) / $monthly_rate);
            } else {
                $amount = $this->monthly_contribution * $months;
            }
            
            $this->scenarios[] = [
                'age' => $start_age,
                'label' => "Start at Age {$start_age}",
                'amount' => $amount,
                'months_contributing' => $months,
            ];
        }

        // Calculate cost of waiting (5 years)
        if (count($this->scenarios) > 1) {
            $this->cost_of_waiting = $start_now_amount - $this->scenarios[count($this->scenarios) - 1]['amount'];
        }

        $this->show_results = true;

        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'cost_of_waiting',
                'input_data' => $this->only(['current_age', 'monthly_contribution', 'expected_return', 'retirement_age']),
                'result_data' => [
                    'scenarios' => $this->scenarios,
                    'cost_of_waiting' => $this->cost_of_waiting,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.cost-of-waiting-calculator');
    }
}
