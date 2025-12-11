<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialGoal;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GoalManagement extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingId = null;
    
    // Form fields
    public $name = '';
    public $type = 'emergency_fund';
    public $target_amount = 0;
    public $current_amount = 0;
    public $target_date = '';
    public $monthly_contribution = null;
    public $priority = 'medium';
    public $description = '';
    public $linked_account_id = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:retirement,emergency_fund,property_deposit,travel,debt_payoff,education,other',
        'target_amount' => 'required|numeric|min:0.01|max:999999999.99',
        'current_amount' => 'required|numeric|min:0|max:999999999.99',
        'target_date' => 'required|date|after:today',
        'monthly_contribution' => 'nullable|numeric|min:0|max:999999999.99',
        'priority' => 'required|in:high,medium,low',
        'description' => 'nullable|string',
        'linked_account_id' => 'nullable|exists:financial_accounts,id',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function openForm($goalId = null)
    {
        if ($goalId) {
            $goal = FinancialGoal::where('id', $goalId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $this->editingId = $goal->id;
            $this->name = $goal->name;
            $this->type = $goal->type;
            $this->target_amount = $goal->target_amount;
            $this->current_amount = $goal->current_amount;
            $this->target_date = $goal->target_date->format('Y-m-d');
            $this->monthly_contribution = $goal->monthly_contribution;
            $this->priority = $goal->priority;
            $this->description = $goal->description ?? '';
            $this->linked_account_id = $goal->linked_account_id;
        } else {
            $this->resetForm();
        }
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->type = 'emergency_fund';
        $this->target_amount = 0;
        $this->current_amount = 0;
        $this->target_date = '';
        $this->monthly_contribution = null;
        $this->priority = 'medium';
        $this->description = '';
        $this->linked_account_id = null;
    }

    public function save()
    {
        $this->validate();

        // Ensure linked_account_id belongs to user
        if ($this->linked_account_id) {
            $account = FinancialAccount::where('id', $this->linked_account_id)
                ->where('user_id', Auth::id())
                ->first();
            if (!$account) {
                $this->addError('linked_account_id', 'Invalid account selected.');
                return;
            }
        }

        $data = [
            'user_id' => Auth::id(),
            'name' => $this->name,
            'type' => $this->type,
            'target_amount' => $this->target_amount,
            'current_amount' => $this->current_amount,
            'target_date' => $this->target_date,
            'monthly_contribution' => $this->monthly_contribution,
            'priority' => $this->priority,
            'description' => $this->description ?: null,
            'linked_account_id' => $this->linked_account_id,
        ];

        if ($this->editingId) {
            FinancialGoal::where('id', $this->editingId)
                ->where('user_id', Auth::id())
                ->update($data);
            session()->flash('message', 'Goal updated successfully.');
        } else {
            FinancialGoal::create($data);
            session()->flash('message', 'Goal created successfully.');
        }

        $this->closeForm();
        $this->resetPage();
    }

    public function delete($goalId)
    {
        $goal = FinancialGoal::where('id', $goalId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $goal->delete();
        session()->flash('message', 'Goal deleted successfully.');
        $this->resetPage();
    }

    public function render()
    {
        $goals = FinancialGoal::where('user_id', Auth::id())
            ->orderBy('priority', 'desc')
            ->orderBy('target_date', 'asc')
            ->paginate(10);

        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $goalTypes = [
            'retirement' => 'Retirement',
            'emergency_fund' => 'Emergency Fund',
            'property_deposit' => 'Property Deposit',
            'travel' => 'Travel',
            'debt_payoff' => 'Debt Payoff',
            'education' => 'Education',
            'other' => 'Other',
        ];

        $priorities = [
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];

        return view('livewire.financial-planning.goal-management', [
            'goals' => $goals,
            'accounts' => $accounts,
            'goalTypes' => $goalTypes,
            'priorities' => $priorities,
        ]);
    }
}

