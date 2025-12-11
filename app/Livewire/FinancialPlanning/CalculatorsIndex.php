<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CalculatorsIndex extends Component
{
    public $categories = [
        'Retirement & Pension' => [
            [
                'name' => 'Retirement Needs Calculator',
                'description' => 'Calculate how much you need to save for retirement',
                'route' => 'financial.calculators.retirement-needs',
                'icon' => '🏖️',
            ],
            [
                'name' => 'Pension Growth Projector',
                'description' => 'Project your pension growth over time',
                'route' => 'financial.calculators.pension-growth',
                'icon' => '📈',
            ],
            [
                'name' => '4% Rule Retirement Calculator',
                'description' => 'Calculate retirement needs using the 4% rule',
                'route' => 'financial.calculators.four-percent-rule',
                'icon' => '💰',
            ],
            [
                'name' => 'Cost of Waiting Calculator',
                'description' => 'See how much waiting costs you in retirement savings',
                'route' => 'financial.calculators.cost-of-waiting',
                'icon' => '⏰',
            ],
            [
                'name' => 'Early Retirement (FIRE) Calculator',
                'description' => 'Calculate when you can achieve financial independence',
                'route' => 'financial.calculators.fire',
                'icon' => '🔥',
            ],
        ],
        'Investment Calculators' => [
            [
                'name' => 'Compound Interest Calculator',
                'description' => 'See how your investments grow with compound interest',
                'route' => 'financial.calculators.compound-interest',
                'icon' => '📊',
            ],
            [
                'name' => 'Investment Return Projector',
                'description' => 'Project future investment returns',
                'route' => 'financial.calculators.investment-return',
                'icon' => '📈',
            ],
            [
                'name' => 'Asset Allocation Analyzer',
                'description' => 'Get recommended asset allocation based on your profile',
                'route' => 'financial.calculators.asset-allocation',
                'icon' => '⚖️',
            ],
            [
                'name' => 'Portfolio Risk Calculator',
                'description' => 'Assess your portfolio risk level',
                'route' => 'financial.calculators.portfolio-risk',
                'icon' => '⚠️',
            ],
            [
                'name' => 'Dollar-Cost Averaging Simulator',
                'description' => 'Compare lump sum vs DCA investment strategies',
                'route' => 'financial.calculators.dca-simulator',
                'icon' => '💵',
            ],
            [
                'name' => 'Dividend Income Calculator',
                'description' => 'Calculate passive income from dividends',
                'route' => 'financial.calculators.dividend-income',
                'icon' => '💸',
            ],
        ],
        'Savings & Budget' => [
            [
                'name' => 'Yacht Crew Budget Calculator',
                'description' => 'Plan your budget for working and time-off periods',
                'route' => 'financial.calculators.yacht-crew-budget',
                'icon' => '⚓',
            ],
            [
                'name' => 'Emergency Fund Calculator',
                'description' => 'Calculate how much emergency fund you need',
                'route' => 'financial.calculators.emergency-fund',
                'icon' => '🆘',
            ],
            [
                'name' => 'Savings Rate Calculator',
                'description' => 'Calculate and optimize your savings rate',
                'route' => 'financial.calculators.savings-rate',
                'icon' => '💾',
            ],
            [
                'name' => 'Time-Off Expense Planner',
                'description' => 'Plan expenses for your time off',
                'route' => 'financial.calculators.time-off-expense',
                'icon' => '🏝️',
            ],
        ],
        'Debt & Loans' => [
            [
                'name' => 'Debt Payoff Calculator',
                'description' => 'Find the best strategy to pay off debt',
                'route' => 'financial.calculators.debt-payoff',
                'icon' => '💳',
            ],
            [
                'name' => 'Mortgage Affordability Calculator',
                'description' => 'Calculate how much mortgage you can afford',
                'route' => 'financial.calculators.mortgage-affordability',
                'icon' => '🏠',
            ],
            [
                'name' => 'Buy vs Rent Calculator',
                'description' => 'Compare buying vs renting a property',
                'route' => 'financial.calculators.buy-vs-rent',
                'icon' => '🏘️',
            ],
        ],
        'Property Investment' => [
            [
                'name' => 'Rental Property Analyzer',
                'description' => 'Analyze rental property investment returns',
                'route' => 'financial.calculators.rental-property',
                'icon' => '🏘️',
            ],
            [
                'name' => 'Property Appreciation Calculator',
                'description' => 'Project property value appreciation',
                'route' => 'financial.calculators.property-appreciation',
                'icon' => '📈',
            ],
            [
                'name' => 'Real Estate ROI Calculator',
                'description' => 'Calculate return on real estate investment',
                'route' => 'financial.calculators.real-estate-roi',
                'icon' => '💰',
            ],
        ],
        'Tax Calculators' => [
            [
                'name' => 'Income Tax Estimator',
                'description' => 'Estimate your tax liability by country',
                'route' => 'financial.calculators.income-tax',
                'icon' => '📋',
            ],
            [
                'name' => 'Tax-Efficient Withdrawal Calculator',
                'description' => 'Optimize retirement withdrawal strategy',
                'route' => 'financial.calculators.tax-efficient-withdrawal',
                'icon' => '💼',
            ],
            [
                'name' => 'Capital Gains Tax Calculator',
                'description' => 'Calculate capital gains tax on investments',
                'route' => 'financial.calculators.capital-gains-tax',
                'icon' => '📊',
            ],
        ],
    ];

    public function render()
    {
        return view('livewire.financial-planning.calculators-index');
    }
}
