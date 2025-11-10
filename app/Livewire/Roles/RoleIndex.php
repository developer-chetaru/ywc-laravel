<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;

class RoleIndex extends Component
{
    use WithPagination;

    public $roleId;
    public $name;
    public $guard_name;
    public $role_id;
    public $isEditMode = false;
    public $search = '';

    // Keep pagination theme
    protected $paginationTheme = 'tailwind';

    public $showModal = false;
    public $status = 'Active';

    // Persist search to query string (optional but useful)
    public $statusFilter = 'all';  // Active / Inactive
    public $sortOrder = 'asc';   // asc = A–Z, desc = Z–A

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortOrder' => ['except' => 'asc'],
    ];

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'guard_name' => 'required',
        'status' => 'required|in:Active,Inactive',
    ];

    // Listen for delete confirmation (Livewire v3 style)
    protected $listeners = ['confirmDelete', 'clear-flash-message' => 'clearFlashMessage'];

    // When user types a new search, reset page to 1
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSortOrder() { $this->resetPage(); }

    public function mount()
    {
        $this->guard_name = 'api';
    }

    public function clearFlashMessage()
    {
        session()->forget(['success', 'error']);
    }

    public function render()
    {
        $term = trim($this->search);

        $rolesQuery = Role::query()->withCount('users');

        // ✅ Search Filter
        if ($term !== '') {
            $rolesQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
            });
        }

         // ✅ Status Filter
        if ($this->statusFilter !== 'all') {
            $rolesQuery->where('status', $this->statusFilter);
        }

        // ✅ Sort Order (A–Z or Z–A)
        $rolesQuery->orderBy('name', $this->sortOrder);

        $roles = $rolesQuery->paginate(5);

        return view('livewire.roles.role-index', compact('roles'))
            ->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->reset(['name', 'role_id', 'isEditMode', 'status']);
        $this->resetValidation();
    }

    
    public function toggleStatus($id)
    {
        $role = Role::find($id);

        if ($role) {
            // Toggle between Active / Inactive
            $role->status = $role->status === 'Active' ? 'Inactive' : 'Active';
            $role->save();

            // Flash message (optional)
            session()->flash('success', 'Role status updated successfully.');

            // Refresh table
            $this->dispatch('refreshData');
        } else {
            session()->flash('error', 'Role not found.');
        }
    }


    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\Role::where('name', $value)
                        ->where('guard_name', $this->guard_name)
                        ->when($this->roleId, function ($query) {
                            $query->where('id', '!=', $this->roleId);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('This role name already exists.');
                    }
                },
            ],
            'guard_name' => 'required|string|max:255',
        ]);

        if ($this->roleId) {
            // ✅ Update existing role
            $role = \App\Models\Role::findOrFail($this->roleId);
            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
                'status' => $this->status,
            ]);
            session()->flash('success', 'Role updated successfully!');
        } else {
            // ✅ Create new role
            \App\Models\Role::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
                'status' => $this->status,
            ]);
            session()->flash('success', 'Role added successfully!');
        }

        $this->dispatch('refreshData');

        // clear message after short delay
        $this->dispatch('clear-flash-message');

        $this->reset(['name', 'guard_name', 'status', 'roleId', 'showModal']);
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->status = $role->status ?? 'Active';
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role_id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $role = Role::findOrFail($this->role_id);
        $role->update([
            'name' => $this->name,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Role updated successfully!');
        $this->reset(['name', 'status', 'showModal']);
        $this->dispatch('refreshData');
        $this->dispatch('clear-flash-message');
    }

    // Trigger SweetAlert (Livewire v3 dispatch)
    public function delete($id)
    {
        // dispatch event your blade JS listens for
        $this->dispatch('confirm-delete', id: $id);
    }

    // Called when JS confirms deletion (listener name 'confirmDelete' above)
    public function confirmDelete($id)
    {
        $role = Role::find($id);

        if (! $role) {
            session()->flash('message', 'Role not found.');
            return;
        }

        $role->delete();

        // Use session flash (your blade shows flash messages)
        session()->flash('message', 'Role deleted successfully.');

        // Also dispatch a small event if your JS shows an inline message (optional)
        $this->dispatch('show-message', message: 'Role deleted successfully.');
    }
}
