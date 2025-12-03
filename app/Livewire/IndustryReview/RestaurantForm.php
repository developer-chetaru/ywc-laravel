<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class RestaurantForm extends Component
{
    use WithFileUploads;

    public $restaurantId = null;
    public $isEditMode = false;

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
    public $cuisine_typeInput = '';
    public $price_range = '';
    public $opening_hoursInput = '';
    public $crew_friendly = false;
    public $crew_discount = false;
    public $crew_discount_details = '';
    public $cover_image;
    public $cover_image_preview = null;
    public $existing_cover_image = null;

    public $loading = false;
    public $error = '';

    public $types = [
        'restaurant' => 'Restaurant',
        'bar' => 'Bar',
        'cafe' => 'Cafe',
        'shop' => 'Shop',
        'service' => 'Service',
    ];

    public $priceRanges = ['€', '€€', '€€€', '€€€€'];

    public function mount($id = null)
    {
        if ($id) {
            $this->restaurantId = $id;
            $this->isEditMode = true;
            $this->loadRestaurant($id);
        }
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
            $this->cuisine_typeInput = is_array($restaurant->cuisine_type) ? implode(', ', $restaurant->cuisine_type) : '';
            $this->price_range = $restaurant->price_range;
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
                session()->flash('success', 'Restaurant updated successfully!');
                return redirect()->route('industryreview.restaurants.manage');
            } else {
                $data['slug'] = Str::slug($this->name);
                Restaurant::create($data);
                session()->flash('success', 'Restaurant created successfully!');
                return redirect()->route('industryreview.restaurants.manage');
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
        return view('livewire.industry-review.restaurant-form');
    }
}

