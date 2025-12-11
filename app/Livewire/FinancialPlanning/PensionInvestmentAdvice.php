<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialAccount;
use App\Models\FinancialGoal;
use App\Models\FinancialTransaction;
use App\Models\FinancialAdvisor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class PensionInvestmentAdvice extends Component
{
    public $activeTab = 'overview';
    
    // Pension Analysis
    public $pensionAccounts = [];
    public $totalPensionValue = 0;
    public $projectedRetirementAge = 65;
    
    // Investment Analysis
    public $investmentAccounts = [];
    public $totalInvestmentValue = 0;
    public $portfolioPerformance = [];
    
    // Advisory
    public $advisors = [];
    public $selectedAdvisor = null;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->loadData();
    }

    public function loadData()
    {
        $user = Auth::user();
        
        // Load pension accounts
        $this->pensionAccounts = FinancialAccount::where('user_id', $user->id)
            ->where('type', 'pension')
            ->where('is_active', true)
            ->get();
        
        $this->totalPensionValue = $this->pensionAccounts->sum('current_balance');
        
        // Load investment accounts
        $this->investmentAccounts = FinancialAccount::where('user_id', $user->id)
            ->where('type', 'investment')
            ->where('is_active', true)
            ->get();
        
        $this->totalInvestmentValue = $this->investmentAccounts->sum('current_balance');
        
        // Load advisors
        $this->advisors = FinancialAdvisor::where('is_active', true)
            ->whereJsonContains('specializations', 'pension')
            ->orWhereJsonContains('specializations', 'investment')
            ->orWhereJsonContains('specializations', 'retirement')
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();
    }

    public function calculateProjection($yearsToRetirement = null)
    {
        $user = Auth::user();
        $currentAge = now()->diffInYears($user->dob ?? now()->subYears(30));
        $retirementAge = $this->projectedRetirementAge;
        $yearsToRetirement = $yearsToRetirement ?? ($retirementAge - $currentAge);
        
        $projections = [];
        
        foreach ($this->pensionAccounts as $account) {
            // Simplified projection - assume 5% annual growth
            $futureValue = $account->current_balance * pow(1.05, $yearsToRetirement);
            $projections[] = [
                'account' => $account->name,
                'current' => $account->current_balance,
                'projected' => $futureValue,
            ];
        }
        
        return $projections;
    }

    public function render()
    {
        $retirementGoals = FinancialGoal::where('user_id', Auth::id())
            ->whereIn('type', ['retirement', 'pension'])
            ->get();
        
        $projections = $this->calculateProjection();
        $totalProjected = collect($projections)->sum('projected');
        
        return view('livewire.financial-planning.pension-investment-advice', [
            'retirementGoals' => $retirementGoals,
            'projections' => $projections,
            'totalProjected' => $totalProjected,
        ]);
    }
}

