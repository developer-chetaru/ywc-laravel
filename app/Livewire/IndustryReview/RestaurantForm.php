<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant;
use App\Models\RestaurantGallery;
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
    public $price_range = null;
    public $opening_hoursInput = '';
    public $crew_friendly = false;
    public $crew_discount = false;
    public $crew_discount_details = '';
    public $cover_image;
    public $cover_image_preview = null;
    public $existing_cover_image = null;

    // Gallery images
    public $gallery_images = [];
    public $gallery_previews = [];
    public $gallery_captions = []; // Captions for new images
    public $existing_gallery = [];
    public $existing_gallery_captions = []; // Captions for existing images
    public $gallery_to_delete = [];

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
            $restaurant = Restaurant::with('gallery')->findOrFail($restaurantId);
            
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
            
            if ($restaurant->cover_image) {
                if (str_starts_with($restaurant->cover_image, 'http')) {
                    $this->existing_cover_image = $restaurant->cover_image;
                } else {
                    $this->existing_cover_image = asset('storage/' . $restaurant->cover_image);
                }
            }

            // Load existing gallery - filter out images without valid paths
            $this->existing_gallery = $restaurant->gallery->filter(function ($item) {
                return !empty($item->image_path);
            })->map(function ($item) {
                // Generate URL manually to ensure it works
                $url = null;
                if ($item->image_path) {
                    if (str_starts_with($item->image_path, 'http')) {
                        $url = $item->image_path;
                    } else {
                        $url = asset('storage/' . $item->image_path);
                    }
                }
                
                return [
                    'id' => $item->id,
                    'url' => $url,
                    'caption' => $item->caption ?? '',
                    'category' => $item->category ?? 'other',
                ];
            })->filter(function ($item) {
                return !empty($item['url']);
            })->values()->toArray();
            
            // Load existing gallery captions
            foreach ($this->existing_gallery as $item) {
                $this->existing_gallery_captions[$item['id']] = $item['caption'] ?? '';
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

    public function updatedGalleryImages()
    {
        $this->gallery_previews = [];
        foreach ($this->gallery_images as $index => $image) {
            if ($image) {
                $this->gallery_previews[$index] = $image->temporaryUrl();
                // Initialize caption if not set
                if (!isset($this->gallery_captions[$index])) {
                    $this->gallery_captions[$index] = '';
                }
            }
        }
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->gallery_images[$index])) {
            unset($this->gallery_images[$index]);
            $this->gallery_images = array_values($this->gallery_images);
        }
        if (isset($this->gallery_previews[$index])) {
            unset($this->gallery_previews[$index]);
            $this->gallery_previews = array_values($this->gallery_previews);
        }
        if (isset($this->gallery_captions[$index])) {
            unset($this->gallery_captions[$index]);
            $this->gallery_captions = array_values($this->gallery_captions);
        }
    }

    public function removeExistingGalleryImage($galleryId)
    {
        $this->gallery_to_delete[] = $galleryId;
        $this->existing_gallery = array_filter($this->existing_gallery, function ($item) use ($galleryId) {
            return $item['id'] != $galleryId;
        });
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
                
                // Handle gallery images
                $this->saveGalleryImages($restaurant);
                
                session()->flash('success', 'Restaurant updated successfully!');
                return redirect()->route('industryreview.restaurants.manage');
            } else {
                $data['slug'] = Str::slug($this->name);
                $restaurant = Restaurant::create($data);
                
                // Handle gallery images
                $this->saveGalleryImages($restaurant);
                
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

    protected function saveGalleryImages($restaurant)
    {
        // Update existing gallery captions
        foreach ($this->existing_gallery as $item) {
            $galleryItem = RestaurantGallery::find($item['id']);
            if ($galleryItem && $galleryItem->restaurant_id == $restaurant->id) {
                $caption = $this->existing_gallery_captions[$item['id']] ?? '';
                $galleryItem->update(['caption' => $caption]);
            }
        }

        // Delete marked gallery images
        foreach ($this->gallery_to_delete as $galleryId) {
            $galleryItem = RestaurantGallery::find($galleryId);
            if ($galleryItem && $galleryItem->restaurant_id == $restaurant->id) {
                if ($galleryItem->image_path && Storage::disk('public')->exists($galleryItem->image_path)) {
                    Storage::disk('public')->delete($galleryItem->image_path);
                }
                $galleryItem->delete();
            }
        }

        // Get current max order
        $maxOrder = $restaurant->gallery()->max('order') ?? 0;

        // Save new gallery images
        foreach ($this->gallery_images as $index => $image) {
            if ($image) {
                $path = $image->store('restaurants/gallery', 'public');
                $caption = $this->gallery_captions[$index] ?? '';
                RestaurantGallery::create([
                    'restaurant_id' => $restaurant->id,
                    'image_path' => $path,
                    'caption' => $caption,
                    'category' => 'other',
                    'order' => $maxOrder + $index + 1,
                    'is_primary' => false,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.industry-review.restaurant-form');
    }
}

