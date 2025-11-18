<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterData;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MasterDataManage extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $editId = null;
    public $showConfirm = false;
    public $deleteId = null;
    public $showForm = false;
    public $selectedType = null; // null = show all types, otherwise show items for selected type

    // Form fields
    public $type = '';
    public $code = '';
    public $name = '';
    public $description = '';
    public $is_active = true;

    protected $updatesQueryString = ['search', 'filterType'];

    public $types = [
        'route_visibility' => 'Route Visibility',
        'route_status' => 'Route Status',
        'marina_type' => 'Marina Type',
        'yacht_type' => 'Yacht Type',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function selectType($type)
    {
        $this->selectedType = $type;
        $this->filterType = $type;
        $this->resetPage();
    }

    public function backToTypes()
    {
        $this->selectedType = null;
        $this->filterType = '';
        $this->search = '';
        $this->resetPage();
    }

    protected function rules()
    {
        $rules = [
            'type' => 'required|string|in:route_visibility,route_status,marina_type,yacht_type',
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        if ($this->editId) {
            $rules['code'] = 'nullable|string|max:255|unique:master_data,code,' . $this->editId . ',id,type,' . $this->type;
        } else {
            $rules['code'] = 'nullable|string|max:255|unique:master_data,code,NULL,id,type,' . $this->type;
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'code' => $this->code ?: null,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ];

        MasterData::create($data);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('message', 'Master data added successfully!');
    }

    public function openAddForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $item = MasterData::findOrFail($id);
        $this->editId = $item->id;
        $this->type = $item->type;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->is_active = $item->is_active;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $item = MasterData::findOrFail($this->editId);
        
        $data = [
            'type' => $this->type,
            'code' => $this->code ?: null,
            'name' => $this->name,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ];


        $item->update($data);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('message', 'Master data updated successfully!');
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showConfirm = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            MasterData::findOrFail($this->deleteId)->delete();
            $this->showConfirm = false;
            $this->deleteId = null;
            session()->flash('message', 'Master data deleted successfully!');
        }
    }

    public function toggleActive($id)
    {
        $item = MasterData::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        session()->flash('message', 'Status updated successfully!');
    }

    protected function resetForm()
    {
        $this->reset(['editId', 'type', 'code', 'name', 'description', 'is_active']);
        $this->is_active = true;
    }

    public function render()
    {
        // Get type counts for the type list view
        $typeCounts = MasterData::query()
            ->where('type', '!=', 'country')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // If no type is selected, show type list (no items query needed)
        if ($this->selectedType === null) {
            return view('livewire.admin.master-data-manage', [
                'items' => collect([]),
                'typeCounts' => $typeCounts,
            ]);
        }

        // If type is selected, show items for that type
        $items = MasterData::query()
            ->where('type', '!=', 'country') // Exclude countries from management
            ->where('type', $this->selectedType)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('code', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.master-data-manage', [
            'items' => $items,
            'typeCounts' => $typeCounts,
        ]);
    }
}
