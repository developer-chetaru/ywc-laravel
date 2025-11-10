<?php

namespace App\Livewire\Certificate;

use Livewire\Component;
use App\Models\CertificateType;
use Livewire\WithPagination;

class CertificateTypeIndex extends Component
{
    public $certificateTypes;
    public $name;
    public $editId = null;
    use WithPagination;
    protected $paginationTheme = 'tailwind';
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:certificate_types,name',
    ];

    public function mount()
    {
        $this->loadCertificateTypes();
    }

    public function loadCertificateTypes()
    {
        $this->certificateTypes = CertificateType::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        CertificateType::create(['name' => $this->name]);

        $this->reset(['name']);
        $this->loadCertificateTypes();
        session()->flash('message', 'Certificate Type added successfully!');
    }

    public function edit($id)
    {
        $certificate = CertificateType::findOrFail($id);
        $this->editId = $id;
        $this->name = $certificate->name;
    }

    public function updateCertificate()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:certificate_types,name,' . $this->editId,
        ]);

        $certificate = CertificateType::findOrFail($this->editId);
        $certificate->update(['name' => $this->name]);

        $this->reset(['name', 'editId']);
        $this->loadCertificateTypes();
        session()->flash('message', 'Certificate Type updated successfully!');
    }

    public function delete($id)
    {
        CertificateType::findOrFail($id)->delete();
        $this->loadCertificateTypes();
        session()->flash('message', 'Certificate Type deleted successfully!');
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'editId']);
    }

    public function toggleActive($id)
    {
        $certificate = CertificateType::findOrFail($id);
        $certificate->update(['is_active' => !$certificate->is_active]);
    }

    public function updatingSearch()
    {
        // reset pagination when searching
        $this->resetPage();
    }

    public function render()
    {
        $types = CertificateType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.certificate.certificate-type-index', [
            'types' => $types,
        ])->layout('layouts.app');
    }
}
