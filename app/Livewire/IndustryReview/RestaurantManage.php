<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class RestaurantManage extends Component
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
    public $restaurantId = null;

    // Form fields
    public $name = '';
    public $type = 'restaurant';
    public $description = '';
    public $address = '';
    public $city = '';
    public $country = '';
    public $latitude = null;
    public $longitude = null;
    public $phone = '';
    public $email = '';
    public $website = '';
    public $cuisine_type = [];
    public $cuisine_typeInput = '';
    public $price_range = '';
    public $opening_hours = [];
    public $opening_hoursInput = '';
    public $crew_friendly = false;
    public $crew_discount = false;
    public $crew_discount_details = '';
    public $cover_image;
    public $cover_image_preview = null;
    public $existing_cover_image = null;

    public $loading = false;
    public $message = '';
    public $error = '';

    public $types = [
        'restaurant' => 'Restaurant',
        'bar' => 'Bar',
        'cafe' => 'Cafe',
        'shop' => 'Shop',
        'service' => 'Service',
    ];

    public $priceRanges = ['€', '€€', '€€€', '€€€€'];

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

    public function openEditModal($restaurantId)
    {
        $this->restaurantId = $restaurantId;
        $this->isEditMode = true;
        $this->loadRestaurant($restaurantId);
        $this->showModal = true;
    }

    public function loadRestaurant($restaurantId)
    {
        $this->loading = true;
        try {
            $restaurant = Restaurant::findOrFail($restaurantId);
            
            $this->name = $restaurant->name;
            $this->type = $restaurant->type;
            $this->description = $restaurant->description;
            $this->address = $restaurant->address;
            $this->city = $restaurant->city;
            $this->country = $restaurant->country;
            $this->latitude = $restaurant->latitude;
            $this->longitude = $restaurant->longitude;
            $this->phone = $restaurant->phone;
            $this->email = $restaurant->email;
            $this->website = $restaurant->website;
            $this->cuisine_type = $restaurant->cuisine_type ?? [];
            $this->cuisine_typeInput = is_array($restaurant->cuisine_type) ? implode(', ', $restaurant->cuisine_type) : '';
            $this->price_range = $restaurant->price_range;
            $this->opening_hours = $restaurant->opening_hours ?? [];
            $this->opening_hoursInput = is_array($restaurant->opening_hours) ? implode(', ', $restaurant->opening_hours) : '';
            $this->crew_friendly = $restaurant->crew_friendly;
            $this->crew_discount = $restaurant->crew_discount;
            $this->crew_discount_details = $restaurant->crew_discount_details;
            
            if ($restaurant->cover_image && !str_starts_with($restaurant->cover_image, 'http')) {
                $this->existing_cover_image = Storage::disk('public')->url($restaurant->cover_image);
            } elseif ($restaurant->cover_image) {
                $this->existing_cover_image = $restaurant->cover_image;
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
            'name', 'type', 'description', 'address', 'city', 'country', 'latitude', 'longitude',
            'phone', 'email', 'website', 'cuisine_type', 'cuisine_typeInput', 'price_range',
            'opening_hours', 'opening_hoursInput', 'crew_friendly', 'crew_discount', 'crew_discount_details',
            'cover_image', 'cover_image_preview', 'existing_cover_image', 'restaurantId'
        ]);
        $this->type = 'restaurant';
        $this->crew_friendly = false;
        $this->crew_discount = false;
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
                'type' => $this->type,
                'description' => $this->description,
                'address' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'cuisine_type' => !empty($this->cuisine_typeInput) ? array_map('trim', explode(',', $this->cuisine_typeInput)) : null,
                'price_range' => $this->price_range,
                'opening_hours' => !empty($this->opening_hoursInput) ? array_map('trim', explode(',', $this->opening_hoursInput)) : null,
                'crew_friendly' => $this->crew_friendly,
                'crew_discount' => $this->crew_discount,
                'crew_discount_details' => $this->crew_discount_details,
            ];

            // Handle cover image upload
            if ($this->cover_image) {
                if ($this->isEditMode && $this->existing_cover_image && !str_starts_with($this->existing_cover_image, 'http')) {
                    if (Storage::disk('public')->exists(str_replace('/storage/', '', $this->existing_cover_image))) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $this->existing_cover_image));
                    }
                }
                $data['cover_image'] = $this->cover_image->store('restaurants', 'public');
            } elseif ($this->isEditMode && $this->existing_cover_image && str_starts_with($this->existing_cover_image, 'http')) {
                $data['cover_image'] = $this->existing_cover_image;
            }

            if ($this->isEditMode) {
                $restaurant = Restaurant::findOrFail($this->restaurantId);
                $restaurant->update($data);
                $this->message = 'Restaurant updated successfully!';
            } else {
                $data['slug'] = Str::slug($this->name);
                Restaurant::create($data);
                $this->message = 'Restaurant created successfully!';
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function deleteRestaurant($restaurantId)
    {
        $this->loading = true;
        try {
            $restaurant = Restaurant::findOrFail($restaurantId);
            if ($restaurant->cover_image && !str_starts_with($restaurant->cover_image, 'http') && Storage::disk('public')->exists($restaurant->cover_image)) {
                Storage::disk('public')->delete($restaurant->cover_image);
            }
            $restaurant->delete();
            $this->message = 'Restaurant deleted successfully!';
            $this->resetPage();
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $query = Restaurant::query()
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
            });

        match($this->sortBy) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('rating_avg')->orderByDesc('reviews_count')->orderBy('name'),
        };

        $restaurants = $query->paginate($this->perPage);

        $restaurants->getCollection()->transform(function ($restaurant) {
            if ($restaurant->cover_image && !str_starts_with($restaurant->cover_image, 'http')) {
                $restaurant->cover_image_url = Storage::disk('public')->url($restaurant->cover_image);
            } elseif ($restaurant->cover_image) {
                $restaurant->cover_image_url = $restaurant->cover_image;
            }
            return $restaurant;
        });

        return view('livewire.industry-review.restaurant-manage', [
            'restaurants' => $restaurants,
        ]);
    }
}
