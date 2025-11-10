<?php

namespace App\Livewire\Certificate;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CertificateIssuer;

class CertificateIssuerIndex extends Component
{
    use WithPagination;

    public $name;
    public $editId = null;
    public $search = '';

    public $showConfirm = false;
    public $deleteId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:certificate_issuers,name',
    ];

    protected $updatesQueryString = ['search'];

    public function updatedSearch()
    {
        $this->resetPage(); // Reset to first page when searching
    }

    public function save()
    {
        $this->validate();

        CertificateIssuer::create([
            'name' => $this->name,
            'is_active' => true,
        ]);

        $this->reset(['name']);
        session()->flash('message', 'Certificate Issuer added successfully!');
    }

    public function edit($id)
    {
        $issuer = CertificateIssuer::findOrFail($id);
        $this->editId = $issuer->id;
        $this->name = $issuer->name;
    }

    public function updateCertificate()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:certificate_issuers,name,' . $this->editId,
        ]);

        $issuer = CertificateIssuer::findOrFail($this->editId);
        $issuer->update(['name' => $this->name]);

        $this->reset(['name', 'editId']);
        session()->flash('message', 'Certificate Issuer updated successfully!');
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'editId']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showConfirm = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            CertificateIssuer::findOrFail($this->deleteId)->delete();
            $this->showConfirm = false;
            $this->deleteId = null;
            session()->flash('message', 'Certificate Issuer deleted successfully!');
        }
    }

    public function toggleActive($id)
    {
        $issuer = CertificateIssuer::findOrFail($id);
        $issuer->update(['is_active' => !$issuer->is_active]);
        session()->flash('message', 'Status updated successfully!');
    }

    public function render()
    {
        $issuers = CertificateIssuer::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.certificate.certificate-issuer-index', [
            'issuers' => $issuers,
        ])->layout('layouts.app');
    }
}