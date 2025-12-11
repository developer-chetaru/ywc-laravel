<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PropertyAppreciationCalculator extends Component
{
    // Inputs
    public $purchase_price = 300000;
    public $current_value = 300000;
    public $appreciation_rate = 3; // Annual %
    public $years = 10;
    public $down_payment = 60000;
    public $annual_rental_income = 24000;
    public $annual_expenses = 12000;

    // Results
    public $future_value = 0;
    public $total_appreciation = 0;
    public $appreciation_amount = 0;
    public $yearly_breakdown = [];
    public $roi_on_equity = 0;
    public $total_return = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'purchase_price' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'appreciation_rate' => 'required|numeric|min:0|max:20',
            'years' => 'required|numeric|min:1|max:100',
            'down_payment' => 'required|numeric|min:0',
            'annual_rental_income' => 'required|numeric|min:0',
            'annual_expenses' => 'required|numeric|min:0',
        ]);

        $current_price = $this->current_value > 0 ? $this->current_value : $this->purchase_price;
        $this->yearly_breakdown = [];

        // Calculate year by year
        for ($year = 1; $year <= $this->years; $year++) {
            $current_price *= (1 + ($this->appreciation_rate / 100));
            
            $appreciation_this_year = $current_price - ($this->current_value > 0 ? $this->current_value : $this->purchase_price);
            $net_rental_income = $this->annual_rental_income - $this->annual_expenses;
            $total_return_this_year = $appreciation_this_year + ($net_rental_income * $year);

            $this->yearly_breakdown[] = [
                'year' => $year,
                'property_value' => round($current_price, 2),
                'appreciation' => round($appreciation_this_year, 2),
                'net_rental' => round($net_rental_income, 2),
                'total_return' => round($total_return_this_year, 2),
            ];
        }

        $this->future_value = round($current_price, 2);
        $this->appreciation_amount = $this->future_value - ($this->current_value > 0 ? $this->current_value : $this->purchase_price);
        $this->total_appreciation = ($this->appreciation_amount / ($this->current_value > 0 ? $this->current_value : $this->purchase_price)) * 100;

        // ROI on equity
        $total_income = ($this->annual_rental_income - $this->annual_expenses) * $this->years;
        $total_return_amount = $this->appreciation_amount + $total_income;
        $this->roi_on_equity = $this->down_payment > 0 ? ($total_return_amount / $this->down_payment) * 100 : 0;
        $this->total_return = $total_return_amount;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'property_appreciation',
                'input_data' => [
                    'purchase_price' => $this->purchase_price,
                    'current_value' => $this->current_value,
                    'appreciation_rate' => $this->appreciation_rate,
                    'years' => $this->years,
                ],
                'result_data' => [
                    'future_value' => $this->future_value,
                    'total_appreciation' => $this->total_appreciation,
                    'roi_on_equity' => $this->roi_on_equity,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.property-appreciation-calculator');
    }
}
