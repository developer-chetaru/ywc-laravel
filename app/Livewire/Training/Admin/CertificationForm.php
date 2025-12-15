<?php

namespace App\Livewire\Training\Admin;

use Livewire\Component;
use App\Models\TrainingCertification;
use App\Models\TrainingCertificationCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class CertificationForm extends Component
{
    public $certificationId = null;
    public $category_id;
    public $name;
    public $slug;
    public $official_designation;
    public $description;
    public $prerequisites;
    public $validity_period_months;
    public $renewal_requirements;
    public $international_recognition;
    public $career_benefits;
    public $positions_requiring;
    public $is_active = true;
    public $requires_admin_approval = false;

    protected $rules = [
        'category_id' => 'required|exists:training_certification_categories,id',
        'name' => 'required|string|max:255',
        'official_designation' => 'nullable|string|max:255',
        'description' => 'required|string',
        'prerequisites' => 'nullable|string',
        'validity_period_months' => 'nullable|integer|min:0',
        'renewal_requirements' => 'nullable|string',
        'international_recognition' => 'nullable|string',
        'career_benefits' => 'nullable|string',
        'positions_requiring' => 'nullable|string',
        'is_active' => 'boolean',
        'requires_admin_approval' => 'boolean',
    ];

    public function mount($id = null)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }

        if ($id) {
            $this->certificationId = $id;
            $cert = TrainingCertification::findOrFail($id);
            $this->category_id = $cert->category_id;
            $this->name = $cert->name;
            $this->slug = $cert->slug;
            $this->official_designation = $cert->official_designation;
            $this->description = $cert->description;
            $this->prerequisites = is_array($cert->prerequisites) ? implode(', ', $cert->prerequisites) : $cert->prerequisites;
            $this->validity_period_months = $cert->validity_period_months;
            $this->renewal_requirements = $cert->renewal_requirements;
            $this->international_recognition = is_array($cert->international_recognition) ? implode(', ', $cert->international_recognition) : $cert->international_recognition;
            $this->career_benefits = is_array($cert->career_benefits) ? implode(', ', $cert->career_benefits) : $cert->career_benefits;
            $this->positions_requiring = is_array($cert->positions_requiring) ? implode(', ', $cert->positions_requiring) : $cert->positions_requiring;
            $this->is_active = $cert->is_active;
            $this->requires_admin_approval = $cert->requires_admin_approval;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'official_designation' => $this->official_designation,
            'description' => $this->description,
            'prerequisites' => $this->prerequisites ? [$this->prerequisites] : null,
            'validity_period_months' => $this->validity_period_months,
            'renewal_requirements' => $this->renewal_requirements,
            'international_recognition' => $this->international_recognition ? [$this->international_recognition] : null,
            'career_benefits' => $this->career_benefits ? [$this->career_benefits] : null,
            'positions_requiring' => $this->positions_requiring ? [$this->positions_requiring] : null,
            'is_active' => $this->is_active,
            'requires_admin_approval' => $this->requires_admin_approval,
        ];

        if ($this->certificationId) {
            $cert = TrainingCertification::findOrFail($this->certificationId);
            $cert->update($data);
            session()->flash('success', 'Certification updated successfully.');
        } else {
            $data['slug'] = Str::slug($this->name);
            TrainingCertification::create($data);
            session()->flash('success', 'Certification created successfully.');
        }

        return redirect()->route('training.admin.certifications');
    }

    public function render()
    {
        $categories = TrainingCertificationCategory::where('is_active', true)->orderBy('name')->get();

        return view('livewire.training.admin.certification-form', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
