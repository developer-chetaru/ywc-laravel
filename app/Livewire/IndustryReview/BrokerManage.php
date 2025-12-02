<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Broker;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class BrokerManage extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $filterType = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'rating';

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    public $showModal = false;
    public $isEditMode = false;
    public $brokerId = null;

    // Form fields
    public $name = '';
    public $business_name = '';
    public $type = 'crew_placement_agency';
    public $description = '';
    public $primary_location = '';
    public $office_locations = [];
    public $office_locationsInput = '';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $specialties = [];
    public $specialtiesInput = '';
    public $fee_structure = '';
    public $regions_served = [];
    public $regions_servedInput = '';
    public $years_in_business = '';
    public $is_myba_member = false;
    public $is_licensed = false;
    public $certifications = [];
    public $certificationsInput = '';
    public $logo;
    public $logo_preview = null;
    public $existing_logo = null;

    public $loading = false;
    public $message = '';
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->sortBy = 'rating';
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($brokerId)
    {
        $this->brokerId = $brokerId;
        $this->isEditMode = true;
        $this->loadBroker($brokerId);
        $this->showModal = true;
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
            $this->office_locations = $broker->office_locations ?? [];
            $this->office_locationsInput = is_array($broker->office_locations) ? implode(', ', $broker->office_locations) : '';
            $this->phone = $broker->phone;
            $this->email = $broker->email;
            $this->website = $broker->website;
            $this->specialties = $broker->specialties ?? [];
            $this->specialtiesInput = is_array($broker->specialties) ? implode(', ', $broker->specialties) : '';
            $this->fee_structure = $broker->fee_structure;
            $this->regions_served = $broker->regions_served ?? [];
            $this->regions_servedInput = is_array($broker->regions_served) ? implode(', ', $broker->regions_served) : '';
            $this->years_in_business = $broker->years_in_business;
            $this->is_myba_member = $broker->is_myba_member;
            $this->is_licensed = $broker->is_licensed;
            $this->certifications = $broker->certifications ?? [];
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

    public function resetForm()
    {
        $this->reset([
            'name', 'business_name', 'type', 'description', 'primary_location', 'office_locations', 'office_locationsInput',
            'phone', 'email', 'website', 'specialties', 'specialtiesInput', 'fee_structure',
            'regions_served', 'regions_servedInput', 'years_in_business', 'is_myba_member', 'is_licensed',
            'certifications', 'certificationsInput', 'logo', 'logo_preview', 'existing_logo', 'brokerId'
        ]);
        $this->type = 'crew_placement_agency';
        $this->is_myba_member = false;
        $this->is_licensed = false;
        $this->message = '';
        $this->error = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->loading = true;
        $this->error = '';
        $this->message = '';

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
                $this->message = 'Broker updated successfully!';
            } else {
                $data['slug'] = Str::slug($this->name);
                Broker::create($data);
                $this->message = 'Broker created successfully!';
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function deleteBroker($brokerId)
    {
        $this->loading = true;
        try {
            $broker = Broker::findOrFail($brokerId);
            if ($broker->logo && !str_starts_with($broker->logo, 'http') && Storage::disk('public')->exists($broker->logo)) {
                Storage::disk('public')->delete($broker->logo);
            }
            $broker->delete();
            $this->message = 'Broker deleted successfully!';
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $query = Broker::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('business_name', 'like', "%{$this->search}%")
                        ->orWhere('primary_location', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, function ($q) {
                $q->where('type', $this->filterType);
            });

        match($this->sortBy) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count')->orderBy('name'),
        };

        $brokers = $query->paginate($this->perPage);

        $brokers->getCollection()->transform(function ($broker) {
            if ($broker->logo && !str_starts_with($broker->logo, 'http')) {
                $broker->logo_url = Storage::disk('public')->url($broker->logo);
            } elseif ($broker->logo) {
                $broker->logo_url = $broker->logo;
            }
            return $broker;
        });

        return view('livewire.industry-review.broker-manage', [
            'brokers' => $brokers,
        ]);
    }
}
