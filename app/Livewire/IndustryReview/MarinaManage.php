<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Marina;
use App\Services\MarinaService;

#[Layout('layouts.app')]
class MarinaManage extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $filterType = '';

    #[Url(as: 'country')]
    public string $filterCountry = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'rating';

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    public $showModal = false;
    public $isEditMode = false;
    public $marinaId = null;

    // Form fields
    public $name = '';
    public $country = '';
    public $region = '';
    public $city = '';
    public $address = '';
    public $type = 'full_service';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $cover_image;
    public $cover_image_preview = null;
    public $existing_cover_image = null;

    public $loading = false;
    public $message = '';
    public $error = '';

    public $types = [
        'full_service' => 'Full Service',
        'municipal_port' => 'Municipal Port',
        'yacht_club' => 'Yacht Club',
        'anchorage' => 'Anchorage',
        'mooring_field' => 'Mooring Field',
        'dry_stack' => 'Dry Stack',
        'boatyard' => 'Boatyard',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterCountry()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterCountry = '';
        $this->sortBy = 'rating';
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($marinaId)
    {
        $this->marinaId = $marinaId;
        $this->isEditMode = true;
        $this->loadMarina($marinaId);
        $this->showModal = true;
    }

    public function loadMarina($marinaId)
    {
        $this->loading = true;
        try {
            $marina = Marina::findOrFail($marinaId);
            
            $this->name = $marina->name;
            $this->country = $marina->country;
            $this->region = $marina->region;
            $this->city = $marina->city;
            $this->address = $marina->address;
            $this->type = $marina->type;
            $this->phone = $marina->phone;
            $this->email = $marina->email;
            $this->website = $marina->website;
            
            if ($marina->cover_image) {
                $this->existing_cover_image = Storage::disk('public')->url($marina->cover_image);
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function updatedCoverImage()
    {
        if ($this->cover_image) {
            $this->cover_image_preview = $this->cover_image->temporaryUrl();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'country', 'region', 'city', 'address', 'type',
            'phone', 'email', 'website', 'cover_image', 'cover_image_preview',
            'existing_cover_image', 'marinaId'
        ]);
        $this->type = 'full_service';
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

            $service = app(MarinaService::class);

            $data = [
                'name' => $this->name,
                'country' => $this->country,
                'type' => $this->type,
            ];

            if ($this->region) $data['region'] = $this->region;
            if ($this->city) $data['city'] = $this->city;
            if ($this->address) $data['address'] = $this->address;
            if ($this->phone) $data['phone'] = $this->phone;
            if ($this->email) $data['email'] = $this->email;
            if ($this->website) $data['website'] = $this->website;

            if ($this->isEditMode) {
                $marina = Marina::findOrFail($this->marinaId);
                $service->update($marina, $data, $this->cover_image);
                $this->message = 'Marina updated successfully!';
            } else {
                $service->create($data, $this->cover_image);
                $this->message = 'Marina created successfully!';
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function deleteMarina($marinaId)
    {
        $this->loading = true;
        try {
            $marina = Marina::findOrFail($marinaId);
            $service = app(MarinaService::class);
            $service->delete($marina);
            $this->message = 'Marina deleted successfully!';
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $query = Marina::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%")
                        ->orWhere('country', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, function ($q) {
                $q->where('type', $this->filterType);
            })
            ->when($this->filterCountry, function ($q) {
                $q->where('country', $this->filterCountry);
            });

        // Apply sorting
        match($this->sortBy) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'reviews_asc' => $query->orderBy('reviews_count'),
            'reviews_desc' => $query->orderByDesc('reviews_count'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count')->orderBy('name'),
        };

        $marinas = $query->paginate($this->perPage);

        // Get unique countries for filter
        $countries = Marina::distinct()->whereNotNull('country')->orderBy('country')->pluck('country');

        // Add cover_image_url
        $marinas->getCollection()->transform(function ($marina) {
            if ($marina->cover_image) {
                $marina->cover_image_url = asset('storage/' . $marina->cover_image);
            } else {
                $marina->cover_image_url = null;
            }
            return $marina;
        });

        return view('livewire.industry-review.marina-manage', [
            'marinas' => $marinas,
            'countries' => $countries,
        ]);
    }
}
