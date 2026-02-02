<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingCertificationCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ManageCategories extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $description = '';
    public $icon = '';
    public $sort_order = 0;
    public $is_active = true;
    public $editId = null;
    public $showForm = false;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:255',
        'sort_order' => 'nullable|integer|min:0',
        'is_active' => 'boolean',
    ];

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

    public function openForm()
    {
        $this->showForm = true;
        $this->resetForm();
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'description', 'icon', 'sort_order', 'is_active', 'editId']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:training_certification_categories,name,' . $this->editId,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order ?? 0,
            'is_active' => $this->is_active,
        ];

        if ($this->editId) {
            $category = TrainingCertificationCategory::findOrFail($this->editId);
            $category->update($data);
            session()->flash('success', 'Category updated successfully.');
        } else {
            TrainingCertificationCategory::create($data);
            session()->flash('success', 'Category created successfully.');
        }

        $this->closeForm();
        $this->resetPage();
    }

    public function edit($id)
    {
        $category = TrainingCertificationCategory::findOrFail($id);
        $this->editId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->icon = $category->icon;
        $this->sort_order = $category->sort_order;
        $this->is_active = $category->is_active;
        $this->showForm = true;
    }

    public function toggleStatus($id)
    {
        $category = TrainingCertificationCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        session()->flash('success', 'Category status updated.');
    }

    public function delete($id)
    {
        $category = TrainingCertificationCategory::findOrFail($id);
        
        // Check if category has certifications
        if ($category->certifications()->count() > 0) {
            session()->flash('error', 'Cannot delete category with existing certifications. Please reassign or delete certifications first.');
            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $query = TrainingCertificationCategory::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('livewire.training.admin.manage-categories', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
