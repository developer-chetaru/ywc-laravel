<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingProvider;
use Illuminate\Support\Facades\Auth;

class ManageProviders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $provider = TrainingProvider::findOrFail($id);
        $provider->update(['is_active' => !$provider->is_active]);
        session()->flash('success', 'Provider status updated.');
    }

    public function toggleVerified($id)
    {
        $provider = TrainingProvider::findOrFail($id);
        $provider->update(['is_verified_partner' => !$provider->is_verified_partner]);
        session()->flash('success', 'Verified partner status updated.');
    }

    public function delete($id)
    {
        $provider = TrainingProvider::findOrFail($id);
        $provider->delete();
        session()->flash('success', 'Provider deleted successfully.');
    }

    public function render()
    {
        $query = TrainingProvider::withCount(['courses', 'activeCourses']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $providers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.training.admin.manage-providers', [
            'providers' => $providers,
        ])->layout('layouts.app');
    }
}
