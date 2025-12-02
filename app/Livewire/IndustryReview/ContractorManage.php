<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Contractor;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class ContractorManage extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public string $filterCategory = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'rating';

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    public $showModal = false;
    public $isEditMode = false;
    public $contractorId = null;

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
    public $specialties = [];
    public $specialtiesInput = '';
    public $languages = [];
    public $languagesInput = '';
    public $emergency_service = false;
    public $response_time = '';
    public $service_area = '';
    public $price_range = '';
    public $logo;
    public $logo_preview = null;
    public $existing_logo = null;

    public $loading = false;
    public $message = '';
    public $error = '';

    public $categories = [
        'technical_services' => 'Technical Services',
        'refit_repair' => 'Refit & Repair',
        'equipment_supplier' => 'Equipment Supplier',
        'professional_services' => 'Professional Services',
        'crew_services' => 'Crew Services',
    ];

    public $priceRanges = ['€', '€€', '€€€', '€€€€'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterCategory = '';
        $this->sortBy = 'rating';
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($contractorId)
    {
        $this->contractorId = $contractorId;
        $this->isEditMode = true;
        $this->loadContractor($contractorId);
        $this->showModal = true;
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
            $this->specialties = $contractor->specialties ?? [];
            $this->specialtiesInput = is_array($contractor->specialties) ? implode(', ', $contractor->specialties) : '';
            $this->languages = $contractor->languages ?? [];
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

    public function resetForm()
    {
        $this->reset([
            'name', 'business_name', 'category', 'description', 'location', 'city', 'country',
            'phone', 'email', 'website', 'specialties', 'specialtiesInput', 'languages', 'languagesInput',
            'emergency_service', 'response_time', 'service_area', 'price_range',
            'logo', 'logo_preview', 'existing_logo', 'contractorId'
        ]);
        $this->category = 'technical_services';
        $this->emergency_service = false;
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
                // Keep external URL if editing and no new logo uploaded
                $data['logo'] = $this->existing_logo;
            }

            if ($this->isEditMode) {
                $contractor = Contractor::findOrFail($this->contractorId);
                $contractor->update($data);
                $this->message = 'Contractor updated successfully!';
            } else {
                $data['slug'] = Str::slug($this->name);
                Contractor::create($data);
                $this->message = 'Contractor created successfully!';
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function deleteContractor($contractorId)
    {
        $this->loading = true;
        try {
            $contractor = Contractor::findOrFail($contractorId);
            if ($contractor->logo && !str_starts_with($contractor->logo, 'http') && Storage::disk('public')->exists($contractor->logo)) {
                Storage::disk('public')->delete($contractor->logo);
            }
            $contractor->delete();
            $this->message = 'Contractor deleted successfully!';
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $query = Contractor::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('business_name', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('country', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory, function ($q) {
                $q->where('category', $this->filterCategory);
            });

        match($this->sortBy) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count')->orderBy('name'),
        };

        $contractors = $query->paginate($this->perPage);

        $contractors->getCollection()->transform(function ($contractor) {
            if ($contractor->logo && !str_starts_with($contractor->logo, 'http')) {
                $contractor->logo_url = Storage::disk('public')->url($contractor->logo);
            } elseif ($contractor->logo) {
                $contractor->logo_url = $contractor->logo;
            }
            return $contractor;
        });

        return view('livewire.industry-review.contractor-manage', [
            'contractors' => $contractors,
        ]);
    }
}
