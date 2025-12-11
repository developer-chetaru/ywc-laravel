<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class BuyVsRentCalculator extends Component
{
    // Inputs
    public $home_price = 300000;
    public $down_payment = 60000;
    public $interest_rate = 4.5;
    public $loan_term_years = 30;
    public $property_tax_rate = 1.2; // Annual % of home value
    public $home_insurance_annual = 1500;
    public $hoa_fees_monthly = 0;
    public $maintenance_rate = 1; // Annual % of home value
    public $monthly_rent = 1500;
    public $rent_increase_rate = 3; // Annual %
    public $home_appreciation_rate = 3; // Annual %
    public $investment_return = 7; // If renting and investing difference
    public $time_horizon_years = 5;

    // Results
    public $buying_total_cost = 0;
    public $renting_total_cost = 0;
    public $buying_net_worth = 0;
    public $renting_net_worth = 0;
    public $difference = 0;
    public $break_even_years = 0;
    public $yearly_breakdown = [];
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'home_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'loan_term_years' => 'required|numeric|min:1|max:50',
            'property_tax_rate' => 'required|numeric|min:0|max:10',
            'home_insurance_annual' => 'required|numeric|min:0',
            'hoa_fees_monthly' => 'required|numeric|min:0',
            'maintenance_rate' => 'required|numeric|min:0|max:10',
            'monthly_rent' => 'required|numeric|min:0',
            'rent_increase_rate' => 'required|numeric|min:0|max:20',
            'home_appreciation_rate' => 'required|numeric|min:0|max:20',
            'investment_return' => 'required|numeric|min:0|max:30',
            'time_horizon_years' => 'required|numeric|min:1|max:100',
        ]);

        $loan_amount = $this->home_price - $this->down_payment;
        $monthly_rate = ($this->interest_rate / 100) / 12;
        $num_payments = $this->loan_term_years * 12;

        // Calculate monthly mortgage payment
        if ($monthly_rate > 0) {
            $monthly_mortgage = $loan_amount * 
                ($monthly_rate * pow(1 + $monthly_rate, $num_payments)) / 
                (pow(1 + $monthly_rate, $num_payments) - 1);
        } else {
            $monthly_mortgage = $loan_amount / $num_payments;
        }

        // Initial values
        $current_home_value = $this->home_price;
        $buying_total_cost = $this->down_payment; // Down payment upfront
        $renting_total_cost = 0;
        $investment_savings = $this->down_payment; // Money saved by not buying
        $principal_paid = 0;
        $this->yearly_breakdown = [];

        // Calculate year by year
        for ($year = 1; $year <= $this->time_horizon_years; $year++) {
            // Home value appreciation
            $current_home_value *= (1 + ($this->home_appreciation_rate / 100));
            
            // Annual buying costs
            $annual_mortgage = $monthly_mortgage * 12;
            $annual_property_tax = $current_home_value * ($this->property_tax_rate / 100);
            $annual_insurance = $this->home_insurance_annual;
            $annual_hoa = $this->hoa_fees_monthly * 12;
            $annual_maintenance = $current_home_value * ($this->maintenance_rate / 100);
            $annual_buying_cost = $annual_mortgage + $annual_property_tax + 
                                 $annual_insurance + $annual_hoa + $annual_maintenance;
            
            // Estimate principal paid (simplified)
            $remaining_loan = max(0, $loan_amount - ($annual_mortgage * $year));
            $principal_paid = $loan_amount - $remaining_loan;
            
            // Annual renting costs
            $current_rent = $this->monthly_rent * pow(1 + ($this->rent_increase_rate / 100), $year - 1) * 12;
            $renting_total_cost += $current_rent;
            
            // Investment growth on savings
            $investment_savings *= (1 + ($this->investment_return / 100));
            
            // Track yearly data
            $this->yearly_breakdown[] = [
                'year' => $year,
                'home_value' => round($current_home_value, 2),
                'buying_cost' => round($annual_buying_cost, 2),
                'rent_cost' => round($current_rent, 2),
                'principal_paid' => round($principal_paid, 2),
            ];
            
            $buying_total_cost += $annual_buying_cost;
        }

        // Net worth calculations
        $equity = $current_home_value - ($loan_amount - $principal_paid);
        $this->buying_net_worth = $equity - $buying_total_cost;
        
        $this->renting_net_worth = $investment_savings - $renting_total_cost;
        
        $this->difference = $this->buying_net_worth - $this->renting_net_worth;
        
        $this->buying_total_cost = $buying_total_cost;
        $this->renting_total_cost = $renting_total_cost;

        // Calculate break-even point (simplified)
        $monthly_buying_cost = ($buying_total_cost / $this->time_horizon_years) / 12;
        $this->break_even_years = 0;
        $running_buying = $this->down_payment;
        $running_rent = 0;
        
        for ($year = 1; $year <= min(30, $this->time_horizon_years); $year++) {
            $monthly_buy_cost = ($monthly_mortgage + ($current_home_value * ($this->property_tax_rate / 100) / 12) + 
                                ($this->home_insurance_annual / 12) + $this->hoa_fees_monthly + 
                                ($current_home_value * ($this->maintenance_rate / 100) / 12)) * 12;
            $running_buying += $monthly_buy_cost;
            
            $current_rent = $this->monthly_rent * pow(1 + ($this->rent_increase_rate / 100), $year - 1) * 12;
            $running_rent += $current_rent;
            
            if ($running_buying <= $running_rent && $this->break_even_years == 0) {
                $this->break_even_years = $year;
            }
        }

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'buy_vs_rent',
                'input_data' => [
                    'home_price' => $this->home_price,
                    'down_payment' => $this->down_payment,
                    'interest_rate' => $this->interest_rate,
                    'monthly_rent' => $this->monthly_rent,
                    'time_horizon_years' => $this->time_horizon_years,
                ],
                'result_data' => [
                    'buying_net_worth' => $this->buying_net_worth,
                    'renting_net_worth' => $this->renting_net_worth,
                    'difference' => $this->difference,
                    'break_even_years' => $this->break_even_years,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.buy-vs-rent-calculator');
    }
}
