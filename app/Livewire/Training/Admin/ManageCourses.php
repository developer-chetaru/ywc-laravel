<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingProviderCourse;
use App\Models\TrainingCertification;
use App\Models\TrainingProvider;
use Illuminate\Support\Facades\Auth;

class ManageCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $providerFilter = '';
    public $certificationFilter = '';
    public $statusFilter = 'all';
    public $showModal = false;
    public $selectedCourse = null;

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

    public function updatingProviderFilter()
    {
        $this->resetPage();
    }

    public function updatingCertificationFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        $course = TrainingProviderCourse::findOrFail($id);
        $course->update([
            'is_active' => true,
            'requires_admin_approval' => false,
        ]);
        session()->flash('success', 'Course approved and activated.');
    }

    public function toggleStatus($id)
    {
        $course = TrainingProviderCourse::findOrFail($id);
        $course->update(['is_active' => !$course->is_active]);
        session()->flash('success', 'Course status updated.');
    }

    public function delete($id)
    {
        $course = TrainingProviderCourse::findOrFail($id);
        $course->delete();
        session()->flash('success', 'Course deleted successfully.');
    }

    public function render()
    {
        $query = TrainingProviderCourse::with(['certification.category', 'provider']);

        if ($this->search) {
            $query->whereHas('certification', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhereHas('provider', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->providerFilter) {
            $query->where('provider_id', $this->providerFilter);
        }

        if ($this->certificationFilter) {
            $query->where('certification_id', $this->certificationFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->statusFilter === 'pending') {
            $query->where('requires_admin_approval', true)->where('is_active', false);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

        $providers = TrainingProvider::where('is_active', true)->orderBy('name')->get();
        $certifications = TrainingCertification::where('is_active', true)->orderBy('name')->get();

        return view('livewire.training.admin.manage-courses', [
            'courses' => $courses,
            'providers' => $providers,
            'certifications' => $certifications,
        ])->layout('layouts.app');
    }
}
