<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $membership = '';
    public $role = '';
    public $sort = 'desc';

    public $showProfilePopup = false;
    public $selectedUser = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'membership' => ['except' => ''],
        'role' => ['except' => ''],
        'sort' => ['except' => 'desc'],
    ];

    public function updated($property)
    {
        if (in_array($property, ['search', 'status', 'membership', 'role', 'sort'])) {
            $this->resetPage();
        }
    }

    public function showProfileDetails($userId)
    {
        $user = User::with('latestSubscription')->find($userId);
        if ($user) {
            $this->selectedUser = $user;
            $this->showProfilePopup = true;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedMembership()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function updatedRole()
    {
        $this->resetPage();
    }

    public function closeProfilePopup()
    {
        $this->showProfilePopup = false;
        $this->selectedUser = null;
    }

    public function resendVerification($userId)
    {
        $user = User::find($userId);

        if ($user && !$user->is_active) {
            // Your email verification logic
            session()->flash('success', 'Verification email resent to ' . $user->email . '.');
        } else {
            session()->flash('error', 'User not found or already verified.');
        }

        $this->closeProfilePopup();
    }

    public function render()
    {
        $query = User::query()
            ->whereHas("roles", function ($q) {
                $q->where("name", "!=", "super_admin");
            })
            ->with(['latestSubscription', 'roles']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orWhere('id', $this->search);
            });
        }

        // Status
        if ($this->status === 'verified') {
            $query->where('is_active', 1);
        } elseif ($this->status === 'unverified') {
            $query->where('is_active', 0);
        }

        // Membership
        if ($this->membership) {
            $query->whereHas('latestSubscription', function ($q) {
                $q->where('interval', $this->membership === 'yearly' ? 'year' : 'month')
                    ->whereIn('status', ['active', 'trialing'])
                    ->whereDate('end_date', '>=', now());
            });
        }

        // Role filter
        if ($this->role) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        // Sorting
        $query->orderBy('created_at', $this->sort);

        $users = $query->paginate(10)->through(function ($user) {
            $sub = $user->latestSubscription;
            $user->membership_type = $sub
                ? ($sub->interval === 'year' ? '1-Year Plan' : ($sub->interval === 'month' ? 'Monthly' : null))
                : '–';

            $user->last_login = $user->last_login ? Carbon::parse($user->last_login)->format('d-m-Y') : '–';
            $user->first_login = $user->created_at ? Carbon::parse($user->created_at)->format('d-m-Y') : '–';

            // Get user roles (excluding super_admin)
            $user->user_roles = $user->roles->where('name', '!=', 'super_admin')->pluck('name')->toArray();

            return $user;
        });

        // Get all roles for filter dropdown (excluding super_admin)
        $roles = Role::where('name', '!=', 'super_admin')
            ->where('guard_name', 'api')
            ->orderBy('name')
            ->get();

        return view('livewire.user-list', [
            'users' => $users,
            'roles' => $roles
        ])->layout('layouts.app');
    }

}
