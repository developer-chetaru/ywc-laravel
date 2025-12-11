<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialTaxAnalysis;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class TaxPlanner extends Component
{
    // Questionnaire
    public $nationality = '';
    public $current_residence = '';
    public $days_in_countries = [];
    public $permanent_address = '';
    public $voting_registration = '';
    public $bank_account_location = '';
    public $employment_contract_location = '';
    
    // Results
    public $taxResidencyAnalysis = null;
    public $taxObligations = [];
    public $optimizationOpportunities = [];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Load existing analysis if exists
        $existing = FinancialTaxAnalysis::where('user_id', Auth::id())->first();
        if ($existing) {
            $this->nationality = $existing->nationality ?? '';
            $this->current_residence = $existing->current_residence ?? '';
            $this->days_in_countries = $existing->days_in_countries ?? [];
            $this->permanent_address = $existing->permanent_address ?? '';
            $this->taxResidencyAnalysis = $existing->tax_residency_analysis;
            $this->taxObligations = $existing->tax_obligations ?? [];
            $this->optimizationOpportunities = $existing->optimization_opportunities ?? [];
        }
    }

    public function analyze()
    {
        $this->validate([
            'nationality' => 'required|string',
            'current_residence' => 'required|string',
        ]);

        // Simplified tax residency analysis
        $residencyDetermination = [];
        $obligations = [];
        $opportunities = [];

        // Basic logic (simplified - in production would use complex tax rules)
        if ($this->current_residence && count($this->days_in_countries) > 0) {
            $primaryCountry = array_key_first($this->days_in_countries);
            $days = $this->days_in_countries[$primaryCountry] ?? 0;
            
            if ($days > 183) {
                $residencyDetermination[$primaryCountry] = 'Likely Resident';
                $obligations[$primaryCountry] = 'Full tax liability on worldwide income';
            } else {
                $residencyDetermination[$primaryCountry] = 'Likely Non-Resident';
                $obligations[$primaryCountry] = 'Tax only on income sourced in this country';
            }
        }

        // Seafarer's earnings deduction check
        if (strtolower($this->employment_contract_location ?? '') === 'international' || 
            strtolower($this->current_residence ?? '') === 'international') {
            $opportunities[] = 'Seafarer\'s Earnings Deduction may apply - consult tax advisor';
        }

        $this->taxResidencyAnalysis = $residencyDetermination;
        $this->taxObligations = $obligations;
        $this->optimizationOpportunities = $opportunities;

        // Save analysis
        FinancialTaxAnalysis::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'nationality' => $this->nationality,
                'current_residence' => $this->current_residence,
                'days_in_countries' => $this->days_in_countries,
                'permanent_address' => $this->permanent_address,
                'tax_residency_analysis' => $this->taxResidencyAnalysis,
                'tax_obligations' => $this->taxObligations,
                'optimization_opportunities' => $this->optimizationOpportunities,
            ]
        );

        session()->flash('message', 'Tax analysis completed successfully.');
    }

    public function render()
    {
        return view('livewire.financial-planning.tax-planner');
    }
}

