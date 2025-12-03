<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Contractor;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class ContractorForm extends Component
{
    use WithFileUploads;

    public $contractorId = null;
    public $isEditMode = false;

    // Form fields
    public $name = '';
    public $business_name = '';
    public $category = 'technical_services';
    public $description = '';
    public $location = '';
    public $city = '';
    public $country = '';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $specialtiesInput = '';
    public $languagesInput = '';
    public $emergency_service = false;
    public $response_time = '';
    public $service_area = '';
    public $price_range = '';
    public $logo;
    public $logo_preview = null;
    public $existing_logo = null;

    public $loading = false;
    public $error = '';

    public $categories = [
        'technical_services' => 'Technical Services',
        'refit_repair' => 'Refit & Repair',
        'equipment_supplier' => 'Equipment Supplier',
        'professional_services' => 'Professional Services',
        'crew_services' => 'Crew Services',
    ];

    public $priceRanges = ['€', '€€', '€€€', '€€€€'];

    public function mount($id = null)
    {
        if ($id) {
            $this->contractorId = $id;
            $this->isEditMode = true;
            $this->loadContractor($id);
        }
    }

    public function loadContractor($contractorId)
    {
        $this->loading = true;
        try {
            $contractor = Contractor::findOrFail($contractorId);
            
            $this->name = $contractor->name;
            $this->business_name = $contractor->business_name;
            $this->category = $contractor->category;
            $this->description = $contractor->description;
            $this->location = $contractor->location;
            $this->city = $contractor->city;
            $this->country = $contractor->country;
            $this->phone = $contractor->phone;
            $this->email = $contractor->email;
            $this->website = $contractor->website;
            $this->specialtiesInput = is_array($contractor->specialties) ? implode(', ', $contractor->specialties) : '';
            $this->languagesInput = is_array($contractor->languages) ? implode(', ', $contractor->languages) : '';
            $this->emergency_service = $contractor->emergency_service;
            $this->response_time = $contractor->response_time;
            $this->service_area = $contractor->service_area;
            $this->price_range = $contractor->price_range;
            
            if ($contractor->logo && !str_starts_with($contractor->logo, 'http')) {
                $this->existing_logo = Storage::disk('public')->url($contractor->logo);
            } elseif ($contractor->logo) {
                $this->existing_logo = $contractor->logo;
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function updatedLogo()
    {
        if ($this->logo) {
            $this->logo_preview = $this->logo->temporaryUrl();
        }
    }

    public function save()
    {
        $this->loading = true;
        $this->error = '';

        try {
            if (!auth()->check()) {
                $this->error = 'You must be logged in.';
                $this->loading = false;
                return;
            }

            $data = [
                'name' => $this->name,
                'business_name' => $this->business_name,
                'category' => $this->category,
                'description' => $this->description,
                'location' => $this->location,
                'city' => $this->city,
                'country' => $this->country,
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'specialties' => !empty($this->specialtiesInput) ? array_map('trim', explode(',', $this->specialtiesInput)) : [],
                'languages' => !empty($this->languagesInput) ? array_map('trim', explode(',', $this->languagesInput)) : [],
                'emergency_service' => $this->emergency_service,
                'response_time' => $this->response_time,
                'service_area' => $this->service_area,
                'price_range' => $this->price_range,
            ];

            // Handle logo upload
            if ($this->logo) {
                if ($this->isEditMode && $this->existing_logo && !str_starts_with($this->existing_logo, 'http')) {
                    if (Storage::disk('public')->exists(str_replace('/storage/', '', $this->existing_logo))) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $this->existing_logo));
                    }
                }
                $data['logo'] = $this->logo->store('contractors', 'public');
            } elseif ($this->isEditMode && $this->existing_logo && str_starts_with($this->existing_logo, 'http')) {
                $data['logo'] = $this->existing_logo;
            }

            if ($this->isEditMode) {
                $contractor = Contractor::findOrFail($this->contractorId);
                $contractor->update($data);
                session()->flash('success', 'Contractor updated successfully!');
                return redirect()->route('industryreview.contractors.manage');
            } else {
                $data['slug'] = Str::slug($this->name);
                Contractor::create($data);
                session()->flash('success', 'Contractor created successfully!');
                return redirect()->route('industryreview.contractors.manage');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.industry-review.contractor-form');
    }
}

