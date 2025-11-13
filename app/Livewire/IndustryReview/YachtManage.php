<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Yacht;
use App\Services\YachtService;

#[Layout('layouts.app')]
class YachtManage extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $filterType = '';

    #[Url(as: 'status')]
    public string $filterStatus = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'rating';

    #[Url(as: 'per_page')]
    public int $perPage = 15;

    public $showModal = false;
    public $isEditMode = false;
    public $yachtId = null;

    // Form fields
    public $name = '';
    public $type = '';
    public $length_meters = '';
    public $length_feet = '';
    public $year_built = '';
    public $flag_registry = '';
    public $home_port = '';
    public $crew_capacity = '';
    public $guest_capacity = '';
    public $status = 'charter';
    public $cover_image;
    public $cover_image_preview = null;
    public $existing_cover_image = null;

    public $loading = false;
    public $message = '';
    public $error = '';

    public $types = [
        'motor_yacht' => 'Motor Yacht',
        'sailing_yacht' => 'Sailing Yacht',
        'explorer' => 'Explorer',
        'catamaran' => 'Catamaran',
        'other' => 'Other',
    ];

    public $statuses = [
        'charter' => 'Charter',
        'private' => 'Private',
        'both' => 'Both',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
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
        $this->filterStatus = '';
        $this->sortBy = 'rating';
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($yachtId)
    {
        $this->yachtId = $yachtId;
        $this->isEditMode = true;
        $this->loadYacht($yachtId);
        $this->showModal = true;
    }

    public function loadYacht($yachtId)
    {
        $this->loading = true;
        try {
            $yacht = Yacht::findOrFail($yachtId);
            
            $this->name = $yacht->name;
            $this->type = $yacht->type;
            $this->length_meters = $yacht->length_meters;
            $this->length_feet = $yacht->length_feet;
            $this->year_built = $yacht->year_built;
            $this->flag_registry = $yacht->flag_registry;
            $this->home_port = $yacht->home_port;
            $this->crew_capacity = $yacht->crew_capacity;
            $this->guest_capacity = $yacht->guest_capacity;
            $this->status = $yacht->status;
            
            if ($yacht->cover_image) {
                $this->existing_cover_image = Storage::disk('public')->url($yacht->cover_image);
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
            'name', 'type', 'length_meters', 'length_feet', 'year_built',
            'flag_registry', 'home_port', 'crew_capacity', 'guest_capacity',
            'status', 'cover_image', 'cover_image_preview', 'existing_cover_image', 'yachtId'
        ]);
        $this->status = 'charter';
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

            $service = app(YachtService::class);

            $data = [
                'name' => $this->name,
                'type' => $this->type,
                'status' => $this->status,
            ];

            if ($this->length_meters) $data['length_meters'] = $this->length_meters;
            if ($this->length_feet) $data['length_feet'] = $this->length_feet;
            if ($this->year_built) $data['year_built'] = $this->year_built;
            if ($this->flag_registry) $data['flag_registry'] = $this->flag_registry;
            if ($this->home_port) $data['home_port'] = $this->home_port;
            if ($this->crew_capacity) $data['crew_capacity'] = $this->crew_capacity;
            if ($this->guest_capacity) $data['guest_capacity'] = $this->guest_capacity;

            if ($this->isEditMode) {
                $yacht = Yacht::findOrFail($this->yachtId);
                $service->update($yacht, $data, $this->cover_image);
                $this->message = 'Yacht updated successfully!';
            } else {
                $service->create($data, $this->cover_image);
                $this->message = 'Yacht created successfully!';
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

    public function deleteYacht($yachtId)
    {
        $this->loading = true;
        try {
            $yacht = Yacht::findOrFail($yachtId);
            $service = app(YachtService::class);
            $service->delete($yacht);
            $this->message = 'Yacht deleted successfully!';
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $query = Yacht::query()
            ->withCount('reviews')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('home_port', 'like', "%{$this->search}%")
                        ->orWhere('builder', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, function ($q) {
                $q->where('type', $this->filterType);
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            });

        // Apply sorting
        match($this->sortBy) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'length_asc' => $query->orderBy('length_meters'),
            'length_desc' => $query->orderByDesc('length_meters'),
            'reviews_asc' => $query->orderBy('reviews_count'),
            'reviews_desc' => $query->orderByDesc('reviews_count'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count')->orderBy('name'),
        };

        $yachts = $query->paginate($this->perPage);

        // Add cover_image_url
        $yachts->getCollection()->transform(function ($yacht) {
            if ($yacht->cover_image) {
                $yacht->cover_image_url = asset('storage/' . $yacht->cover_image);
            } else {
                $yacht->cover_image_url = null;
            }
            return $yacht;
        });

        return view('livewire.industry-review.yacht-manage', [
            'yachts' => $yachts,
        ]);
    }
}
