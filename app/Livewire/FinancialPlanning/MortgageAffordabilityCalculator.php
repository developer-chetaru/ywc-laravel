<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MortgageAffordabilityCalculator extends Component
{
    // Inputs
    public $annual_income = 80000;
    public $monthly_debt_payments = 500;
    public $down_payment = 20000;
    public $interest_rate = 4.5;
    public $loan_term_years = 30;
    public $property_tax_annual = 3000;
    public $home_insurance_annual = 1200;
    public $hoa_fees_monthly = 0;
    public $dti_ratio_limit = 43; // Debt-to-income ratio limit (%)

    // Results
    public $monthly_income = 0;
    public $max_monthly_payment = 0;
    public $max_loan_amount = 0;
    public $max_home_price = 0;
    public $monthly_payment = 0;
    public $principal_interest = 0;
    public $total_monthly_payment = 0;
    public $dti_ratio = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'annual_income' => 'required|numeric|min:0',
            'monthly_debt_payments' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'loan_term_years' => 'required|numeric|min:1|max:50',
            'property_tax_annual' => 'required|numeric|min:0',
            'home_insurance_annual' => 'required|numeric|min:0',
            'hoa_fees_monthly' => 'required|numeric|min:0',
            'dti_ratio_limit' => 'required|numeric|min:20|max:50',
        ]);

        // Calculate monthly income
        $this->monthly_income = $this->annual_income / 12;

        // Calculate max monthly payment based on DTI ratio
        $max_debt_monthly = ($this->annual_income / 12) * ($this->dti_ratio_limit / 100);
        $available_for_housing = $max_debt_monthly - $this->monthly_debt_payments;
        
        // Monthly housing costs (tax, insurance, HOA)
        $monthly_tax = $this->property_tax_annual / 12;
        $monthly_insurance = $this->home_insurance_annual / 12;
        $monthly_housing_costs = $monthly_tax + $monthly_insurance + $this->hoa_fees_monthly;

        // Maximum principal + interest payment
        $this->max_monthly_payment = max(0, $available_for_housing - $monthly_housing_costs);

        // Calculate max loan amount from monthly payment
        if ($this->max_monthly_payment > 0 && $this->interest_rate > 0) {
            $monthly_rate = ($this->interest_rate / 100) / 12;
            $num_payments = $this->loan_term_years * 12;

            if ($monthly_rate > 0) {
                $this->max_loan_amount = $this->max_monthly_payment * 
                    ((1 - pow(1 + $monthly_rate, -$num_payments)) / $monthly_rate);
            } else {
                $this->max_loan_amount = $this->max_monthly_payment * $num_payments;
            }
        } else {
            $this->max_loan_amount = 0;
        }

        // Max home price = loan amount + down payment
        $this->max_home_price = $this->max_loan_amount + $this->down_payment;

        // Calculate actual payment for max home price
        if ($this->max_loan_amount > 0) {
            $monthly_rate = ($this->interest_rate / 100) / 12;
            $num_payments = $this->loan_term_years * 12;

            if ($monthly_rate > 0) {
                $this->principal_interest = $this->max_loan_amount * 
                    ($monthly_rate * pow(1 + $monthly_rate, $num_payments)) / 
                    (pow(1 + $monthly_rate, $num_payments) - 1);
            } else {
                $this->principal_interest = $this->max_loan_amount / $num_payments;
            }
        }

        $this->total_monthly_payment = $this->principal_interest + $monthly_tax + 
                                       $monthly_insurance + $this->hoa_fees_monthly;

        // Calculate actual DTI ratio
        $total_monthly_debt = $this->total_monthly_payment + $this->monthly_debt_payments;
        $this->dti_ratio = $this->monthly_income > 0 ? 
            ($total_monthly_debt / $this->monthly_income) * 100 : 0;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'mortgage_affordability',
                'input_data' => [
                    'annual_income' => $this->annual_income,
                    'monthly_debt_payments' => $this->monthly_debt_payments,
                    'down_payment' => $this->down_payment,
                    'interest_rate' => $this->interest_rate,
                    'loan_term_years' => $this->loan_term_years,
                    'property_tax_annual' => $this->property_tax_annual,
                    'home_insurance_annual' => $this->home_insurance_annual,
                    'hoa_fees_monthly' => $this->hoa_fees_monthly,
                ],
                'result_data' => [
                    'max_home_price' => $this->max_home_price,
                    'max_loan_amount' => $this->max_loan_amount,
                    'total_monthly_payment' => $this->total_monthly_payment,
                    'dti_ratio' => $this->dti_ratio,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.mortgage-affordability-calculator');
    }
}
