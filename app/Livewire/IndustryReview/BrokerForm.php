<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Broker;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class BrokerForm extends Component
{
    use WithFileUploads;

    public $brokerId = null;
    public $isEditMode = false;

    // Form fields
    public $name = '';
    public $business_name = '';
    public $type = 'crew_placement_agency';
    public $description = '';
    public $primary_location = '';
    public $office_locationsInput = '';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $specialtiesInput = '';
    public $fee_structure = '';
    public $regions_servedInput = '';
    public $years_in_business = '';
    public $is_myba_member = false;
    public $is_licensed = false;
    public $certificationsInput = '';
    public $logo;
    public $logo_preview = null;
    public $existing_logo = null;

    public $loading = false;
    public $error = '';

    public $types = [
        'crew_placement_agency' => 'Crew Placement Agency',
        'yacht_management' => 'Yacht Management',
        'independent_broker' => 'Independent Broker',
        'charter_broker' => 'Charter Broker',
    ];

    public $feeStructures = [
        'free_for_crew' => 'Free for Crew',
        'crew_pays' => 'Crew Pays',
        'yacht_pays' => 'Yacht Pays',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->brokerId = $id;
            $this->isEditMode = true;
            $this->loadBroker($id);
        }
    }

    public function loadBroker($brokerId)
    {
        $this->loading = true;
        try {
            $broker = Broker::findOrFail($brokerId);
            
            $this->name = $broker->name;
            $this->business_name = $broker->business_name;
            $this->type = $broker->type;
            $this->description = $broker->description;
            $this->primary_location = $broker->primary_location;
            $this->office_locationsInput = is_array($broker->office_locations) ? implode(', ', $broker->office_locations) : '';
            $this->phone = $broker->phone;
            $this->email = $broker->email;
            $this->website = $broker->website;
            $this->specialtiesInput = is_array($broker->specialties) ? implode(', ', $broker->specialties) : '';
            $this->fee_structure = $broker->fee_structure;
            $this->regions_servedInput = is_array($broker->regions_served) ? implode(', ', $broker->regions_served) : '';
            $this->years_in_business = $broker->years_in_business;
            $this->is_myba_member = $broker->is_myba_member;
            $this->is_licensed = $broker->is_licensed;
            $this->certificationsInput = is_array($broker->certifications) ? implode(', ', $broker->certifications) : '';
            
            if ($broker->logo && !str_starts_with($broker->logo, 'http')) {
                $this->existing_logo = Storage::disk('public')->url($broker->logo);
            } elseif ($broker->logo) {
                $this->existing_logo = $broker->logo;
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
                'type' => $this->type,
                'description' => $this->description,
                'primary_location' => $this->primary_location,
                'office_locations' => !empty($this->office_locationsInput) ? array_map('trim', explode(',', $this->office_locationsInput)) : [],
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'specialties' => !empty($this->specialtiesInput) ? array_map('trim', explode(',', $this->specialtiesInput)) : [],
                'fee_structure' => $this->fee_structure,
                'regions_served' => !empty($this->regions_servedInput) ? array_map('trim', explode(',', $this->regions_servedInput)) : [],
                'years_in_business' => $this->years_in_business,
                'is_myba_member' => $this->is_myba_member,
                'is_licensed' => $this->is_licensed,
                'certifications' => !empty($this->certificationsInput) ? array_map('trim', explode(',', $this->certificationsInput)) : [],
            ];

            // Handle logo upload
            if ($this->logo) {
                if ($this->isEditMode && $this->existing_logo && !str_starts_with($this->existing_logo, 'http')) {
                    if (Storage::disk('public')->exists(str_replace('/storage/', '', $this->existing_logo))) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $this->existing_logo));
                    }
                }
                $data['logo'] = $this->logo->store('brokers', 'public');
            } elseif ($this->isEditMode && $this->existing_logo && str_starts_with($this->existing_logo, 'http')) {
                $data['logo'] = $this->existing_logo;
            }

            if ($this->isEditMode) {
                $broker = Broker::findOrFail($this->brokerId);
                $broker->update($data);
                session()->flash('success', 'Broker updated successfully!');
                return redirect()->route('industryreview.brokers.manage');
            } else {
                $data['slug'] = Str::slug($this->name);
                Broker::create($data);
                session()->flash('success', 'Broker created successfully!');
                return redirect()->route('industryreview.brokers.manage');
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
        return view('livewire.industry-review.broker-form');
    }
}

