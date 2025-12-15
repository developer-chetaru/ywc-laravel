<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingCertification;
use App\Models\TrainingCertificationCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManageCertifications extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
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

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }


    public function approve($id)
    {
        $cert = TrainingCertification::findOrFail($id);
        $cert->update([
            'is_active' => true,
            'requires_admin_approval' => false,
        ]);
        session()->flash('success', 'Certification approved and activated.');
    }

    public function toggleStatus($id)
    {
        $cert = TrainingCertification::findOrFail($id);
        $cert->update(['is_active' => !$cert->is_active]);
        session()->flash('success', 'Certification status updated.');
    }

    public function delete($id)
    {
        $cert = TrainingCertification::findOrFail($id);
        $cert->delete();
        session()->flash('success', 'Certification deleted successfully.');
    }

    public function render()
    {
        $query = TrainingCertification::with('category');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('official_designation', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->statusFilter === 'pending') {
            $query->where('requires_admin_approval', true)->where('is_active', false);
        }

        $certifications = $query->orderBy('created_at', 'desc')->paginate(15);

        $categories = TrainingCertificationCategory::where('is_active', true)->orderBy('name')->get();

        return view('livewire.training.admin.manage-certifications', [
            'certifications' => $certifications,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
