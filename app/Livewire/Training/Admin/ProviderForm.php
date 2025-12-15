<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use App\Models\TrainingProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class ProviderForm extends Component
{
    public $providerId = null;
    public $name;
    public $description;
    public $email;
    public $phone;
    public $website;
    public $is_active = true;
    public $is_verified_partner = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'email' => 'nullable|email',
        'phone' => 'nullable|string',
        'website' => 'nullable|url',
        'is_active' => 'boolean',
        'is_verified_partner' => 'boolean',
    ];

    public function mount($id = null)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }

        if ($id) {
            $this->providerId = $id;
            $provider = TrainingProvider::findOrFail($id);
            $this->name = $provider->name;
            $this->description = $provider->description;
            $this->email = $provider->email;
            $this->phone = $provider->phone;
            $this->website = $provider->website;
            $this->is_active = $provider->is_active;
            $this->is_verified_partner = $provider->is_verified_partner;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'is_active' => $this->is_active,
            'is_verified_partner' => $this->is_verified_partner,
        ];

        if ($this->providerId) {
            $provider = TrainingProvider::findOrFail($this->providerId);
            $provider->update($data);
            session()->flash('success', 'Provider updated successfully.');
        } else {
            $data['slug'] = Str::slug($this->name);
            TrainingProvider::create($data);
            session()->flash('success', 'Provider created successfully.');
        }

        return redirect()->route('training.admin.providers');
    }

    public function render()
    {
        return view('livewire.training.admin.provider-form')->layout('layouts.app');
    }
}
