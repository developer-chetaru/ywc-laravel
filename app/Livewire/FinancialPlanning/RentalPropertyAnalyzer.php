<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialCalculation;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RentalPropertyAnalyzer extends Component
{
    // Inputs
    public $property_price = 250000;
    public $down_payment = 50000;
    public $loan_term_years = 30;
    public $interest_rate = 5;
    public $monthly_rent = 1800;
    public $vacancy_rate = 5; // Percentage
    public $property_management_rate = 8; // Percentage of rent
    public $maintenance_rate = 1; // Annual % of property value
    public $property_tax_annual = 3000;
    public $insurance_annual = 1500;
    public $hoa_fees_monthly = 0;
    public $appreciation_rate = 3; // Annual %

    // Results
    public $monthly_mortgage = 0;
    public $monthly_expenses = 0;
    public $monthly_cash_flow = 0;
    public $annual_cash_flow = 0;
    public $cash_on_cash_return = 0;
    public $cap_rate = 0;
    public $roi = 0;
    public $gross_rent_multiplier = 0;
    public $debt_service_coverage = 0;
    public $show_results = false;

    public function calculate()
    {
        $this->validate([
            'property_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'loan_term_years' => 'required|numeric|min:1|max:50',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'monthly_rent' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:50',
            'property_management_rate' => 'required|numeric|min:0|max:50',
            'maintenance_rate' => 'required|numeric|min:0|max:10',
            'property_tax_annual' => 'required|numeric|min:0',
            'insurance_annual' => 'required|numeric|min:0',
            'hoa_fees_monthly' => 'required|numeric|min:0',
            'appreciation_rate' => 'required|numeric|min:0|max:20',
        ]);

        $loan_amount = $this->property_price - $this->down_payment;
        $monthly_rate = ($this->interest_rate / 100) / 12;
        $num_payments = $this->loan_term_years * 12;

        // Calculate monthly mortgage payment
        if ($monthly_rate > 0 && $loan_amount > 0) {
            $this->monthly_mortgage = $loan_amount * 
                ($monthly_rate * pow(1 + $monthly_rate, $num_payments)) / 
                (pow(1 + $monthly_rate, $num_payments) - 1);
        } else {
            $this->monthly_mortgage = $loan_amount > 0 ? $loan_amount / $num_payments : 0;
        }

        // Calculate monthly expenses
        $effective_rent = $this->monthly_rent * (1 - ($this->vacancy_rate / 100));
        $management_fees = $effective_rent * ($this->property_management_rate / 100);
        $maintenance_monthly = ($this->property_price * ($this->maintenance_rate / 100)) / 12;
        $property_tax_monthly = $this->property_tax_annual / 12;
        $insurance_monthly = $this->insurance_annual / 12;

        $this->monthly_expenses = $this->monthly_mortgage + $management_fees + 
                                  $maintenance_monthly + $property_tax_monthly + 
                                  $insurance_monthly + $this->hoa_fees_monthly;

        // Calculate cash flow
        $this->monthly_cash_flow = $effective_rent - $this->monthly_expenses;
        $this->annual_cash_flow = $this->monthly_cash_flow * 12;

        // Calculate metrics
        // Cash-on-Cash Return = Annual Cash Flow / Total Cash Invested
        $this->cash_on_cash_return = $this->down_payment > 0 ? 
            ($this->annual_cash_flow / $this->down_payment) * 100 : 0;

        // Cap Rate = Net Operating Income / Property Price
        $noi = ($effective_rent * 12) - ($this->monthly_expenses - $this->monthly_mortgage) * 12;
        $this->cap_rate = $this->property_price > 0 ? ($noi / $this->property_price) * 100 : 0;

        // ROI = (Annual Cash Flow + Appreciation) / Total Investment
        $annual_appreciation = $this->property_price * ($this->appreciation_rate / 100);
        $this->roi = $this->down_payment > 0 ? 
            (($this->annual_cash_flow + $annual_appreciation) / $this->down_payment) * 100 : 0;

        // Gross Rent Multiplier = Property Price / Annual Gross Rent
        $annual_gross_rent = $this->monthly_rent * 12;
        $this->gross_rent_multiplier = $annual_gross_rent > 0 ? 
            $this->property_price / $annual_gross_rent : 0;

        // Debt Service Coverage Ratio = NOI / Annual Debt Service
        $annual_debt_service = $this->monthly_mortgage * 12;
        $this->debt_service_coverage = $annual_debt_service > 0 ? 
            $noi / $annual_debt_service : 0;

        $this->show_results = true;

        // Save calculation
        if (Auth::check()) {
            FinancialCalculation::create([
                'user_id' => Auth::id(),
                'calculator_type' => 'rental_property',
                'input_data' => [
                    'property_price' => $this->property_price,
                    'down_payment' => $this->down_payment,
                    'monthly_rent' => $this->monthly_rent,
                    'interest_rate' => $this->interest_rate,
                ],
                'result_data' => [
                    'monthly_cash_flow' => $this->monthly_cash_flow,
                    'annual_cash_flow' => $this->annual_cash_flow,
                    'cash_on_cash_return' => $this->cash_on_cash_return,
                    'cap_rate' => $this->cap_rate,
                    'roi' => $this->roi,
                ],
                'session_id' => session()->getId(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.financial-planning.rental-property-analyzer');
    }
}
