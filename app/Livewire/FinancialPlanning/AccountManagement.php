<?php

namespace App\Livewire\FinancialPlanning;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AccountManagement extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingId = null;
    
    // Form fields
    public $name = '';
    public $type = 'savings';
    public $account_subtype = '';
    public $current_balance = 0;
    public $institution = '';
    public $account_number = '';
    public $interest_rate = null;
    public $monthly_contribution = null;
    public $notes = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:savings,checking,investment,pension,debt,property,other',
        'account_subtype' => 'nullable|string|max:255',
        'current_balance' => 'required|numeric|min:-999999999.99|max:999999999.99',
        'institution' => 'nullable|string|max:255',
        'account_number' => 'nullable|string|max:255',
        'interest_rate' => 'nullable|numeric|min:0|max:100',
        'monthly_contribution' => 'nullable|numeric|min:0|max:999999999.99',
        'notes' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function openForm($accountId = null)
    {
        if ($accountId) {
            $account = FinancialAccount::where('id', $accountId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $this->editingId = $account->id;
            $this->name = $account->name;
            $this->type = $account->type;
            $this->account_subtype = $account->account_subtype ?? '';
            $this->current_balance = $account->current_balance;
            $this->institution = $account->institution ?? '';
            $this->account_number = $account->account_number ?? '';
            $this->interest_rate = $account->interest_rate;
            $this->monthly_contribution = $account->monthly_contribution;
            $this->notes = $account->notes ?? '';
            $this->is_active = $account->is_active;
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
        $this->type = 'savings';
        $this->account_subtype = '';
        $this->current_balance = 0;
        $this->institution = '';
        $this->account_number = '';
        $this->interest_rate = null;
        $this->monthly_contribution = null;
        $this->notes = '';
        $this->is_active = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'name' => $this->name,
            'type' => $this->type,
            'account_subtype' => $this->account_subtype ?: null,
            'current_balance' => $this->current_balance,
            'institution' => $this->institution ?: null,
            'account_number' => $this->account_number ?: null,
            'interest_rate' => $this->interest_rate,
            'monthly_contribution' => $this->monthly_contribution,
            'notes' => $this->notes ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            FinancialAccount::where('id', $this->editingId)
                ->where('user_id', Auth::id())
                ->update($data);
            session()->flash('message', 'Account updated successfully.');
        } else {
            FinancialAccount::create($data);
            session()->flash('message', 'Account created successfully.');
        }

        $this->closeForm();
        $this->resetPage();
    }

    public function delete($accountId)
    {
        $account = FinancialAccount::where('id', $accountId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $account->delete();
        session()->flash('message', 'Account deleted successfully.');
        $this->resetPage();
    }

    public function toggleActive($accountId)
    {
        $account = FinancialAccount::where('id', $accountId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $account->update(['is_active' => !$account->is_active]);
        session()->flash('message', 'Account status updated.');
    }

    public function render()
    {
        $accounts = FinancialAccount::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $accountTypes = [
            'savings' => 'Savings',
            'checking' => 'Checking',
            'investment' => 'Investment',
            'pension' => 'Pension',
            'debt' => 'Debt',
            'property' => 'Property',
            'other' => 'Other',
        ];

        $subtypes = [
            'savings' => ['emergency_fund' => 'Emergency Fund', 'time_off_fund' => 'Time Off Fund', 'general' => 'General Savings'],
            'investment' => ['brokerage' => 'Brokerage', 'crypto' => 'Cryptocurrency', 'other' => 'Other'],
            'pension' => ['sipp' => 'SIPP (UK)', '401k' => '401(k) (US)', 'superannuation' => 'Superannuation (AU)', 'other' => 'Other'],
            'debt' => ['credit_card' => 'Credit Card', 'loan' => 'Personal Loan', 'mortgage' => 'Mortgage', 'other' => 'Other'],
        ];

        return view('livewire.financial-planning.account-management', [
            'accounts' => $accounts,
            'accountTypes' => $accountTypes,
            'subtypes' => $subtypes[$this->type] ?? [],
        ]);
    }
}

