<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RealEstateROICalculator extends Component
{
    // Inputs
    public $purchase_price = 250000;
    public $down_payment = 50000;
    public $closing_costs = 7500;
    public $renovation_costs = 10000;
    public $annual_rental_income = 24000;
    public $vacancy_rate = 5;
    public $annual_expenses = 12000;
    public $annual_appreciation_rate = 3;
    public $years_held = 10;
    public $selling_price = 0; // If known

    // Results
    public $total_investment = 0;
    public $annual_cash_flow = 0;
    public $total_cash_flow = 0;
    public $appreciation_gain = 0;
    public $total_return = 0;
    public $roi_percentage = 0;
    public $annualized_roi = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'purchase_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'closing_costs' => 'required|numeric|min:0',
            'renovation_costs' => 'required|numeric|min:0',
            'annual_rental_income' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'annual_expenses' => 'required|numeric|min:0',
            'annual_appreciation_rate' => 'required|numeric|min:0|max:20',
            'years_held' => 'required|numeric|min:1|max:100',
            'selling_price' => 'required|numeric|min:0',
        ]);

        // Total initial investment
        $this->total_investment = $this->down_payment + $this->closing_costs + $this->renovation_costs;

        // Calculate annual cash flow
        $effective_rent = $this->annual_rental_income * (1 - ($this->vacancy_rate / 100));
        $this->annual_cash_flow = $effective_rent - $this->annual_expenses;
        $this->total_cash_flow = $this->annual_cash_flow * $this->years_held;

        // Calculate appreciation
        if ($this->selling_price > 0) {
            $this->appreciation_gain = $this->selling_price - $this->purchase_price;
        } else {
            $future_value = $this->purchase_price * pow(1 + ($this->annual_appreciation_rate / 100), $this->years_held);
            $this->appreciation_gain = $future_value - $this->purchase_price;
        }

        // Total return
        $this->total_return = $this->total_cash_flow + $this->appreciation_gain;

        // ROI calculations
        $this->roi_percentage = $this->total_investment > 0 ? ($this->total_return / $this->total_investment) * 100 : 0;
        
        // Annualized ROI = (Ending Value / Beginning Value)^(1/years) - 1
        $ending_value = $this->total_investment + $this->total_return;
        $this->annualized_roi = $this->total_investment > 0 ? 
            (pow($ending_value / $this->total_investment, 1 / $this->years_held) - 1) * 100 : 0;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'real_estate_roi',
                'input_data' => [
                    'purchase_price' => $this->purchase_price,
                    'down_payment' => $this->down_payment,
                    'years_held' => $this->years_held,
                ],
                'result_data' => [
                    'total_return' => $this->total_return,
                    'roi_percentage' => $this->roi_percentage,
                    'annualized_roi' => $this->annualized_roi,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.real-estate-r-o-i-calculator');
    }
}
