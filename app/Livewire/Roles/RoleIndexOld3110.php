<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;

class RoleIndex extends Component
{
    use WithPagination;

    public $name;
    public $guard_name;
    public $role_id;
    public $isEditMode = false;
    public $search = '';

    // Keep pagination theme
    protected $paginationTheme = 'tailwind';

    // Persist search to query string (optional but useful)
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'guard_name' => 'required|string|max:500',
    ];

    // Listen for delete confirmation (Livewire v3 style)
    protected $listeners = ['confirmDelete'];

    // When user types a new search, reset page to 1
    public function updatingSearch()
    {
        $this->resetPage();
    }

	public function mount()
    {
        $this->guard_name = 'web';
    }

    public function render()
    {
        $term = trim($this->search);

        $rolesQuery = Role::query()
            ->whereNotIn('name', ['super_admin', 'user']); // 👈 Exclude these roles

        // Apply search only when term is non-empty
        if ($term !== '') {
            $rolesQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                  ->orWhere('guard_name', 'like', '%' . $term . '%');
            });
        }

        $roles = $rolesQuery->latest()->paginate(10);

        return view('livewire.roles.role-index', compact('roles'))
            ->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->reset(['name', 'role_id', 'isEditMode']);
        $this->guard_name = 'web';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        Role::create([
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ]);

        $this->resetForm();
        session()->flash('message', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->isEditMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role_id,
            'guard_name' => 'required|string|max:500',
        ]);

        $role = Role::findOrFail($this->role_id);
        $role->update([
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ]);

        $this->resetForm();
        session()->flash('message', 'Role updated successfully.');
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
