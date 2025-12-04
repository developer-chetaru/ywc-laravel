<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Marina;
use App\Models\MarinaGallery;
use App\Models\MasterData;
use App\Services\MarinaService;

#[Layout('layouts.app')]
class MarinaForm extends Component
{
    use WithFileUploads;

    public $marinaId = null;
    public $isEditMode = false;

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

    // Gallery images
    public $gallery_images = [];
    public $gallery_previews = [];
    public $gallery_captions = []; // Captions for new images
    public $existing_gallery = [];
    public $existing_gallery_captions = []; // Captions for existing images
    public $gallery_to_delete = [];

    public $loading = false;
    public $error = '';

    public function mount($id = null)
    {
        if ($id) {
            $this->marinaId = $id;
            $this->isEditMode = true;
            $this->loadMarina($id);
        }
    }

    public function loadMarina($marinaId)
    {
        $this->loading = true;
        try {
            $marina = Marina::with('gallery')->findOrFail($marinaId);
            
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
                if (str_starts_with($marina->cover_image, 'http')) {
                    $this->existing_cover_image = $marina->cover_image;
                } else {
                    $this->existing_cover_image = asset('storage/' . $marina->cover_image);
                }
            }

            // Load existing gallery - filter out images without valid paths
            $this->existing_gallery = $marina->gallery->filter(function ($item) {
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
                
                // Handle gallery images
                $this->saveGalleryImages($marina);
                
                session()->flash('success', 'Marina updated successfully!');
                return redirect()->route('industryreview.marinas.manage');
            } else {
                $marina = $service->create($data, $this->cover_image);
                
                // Handle gallery images
                $this->saveGalleryImages($marina);
                
                session()->flash('success', 'Marina created successfully!');
                return redirect()->route('industryreview.marinas.manage');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    protected function saveGalleryImages($marina)
    {
        // Update existing gallery captions
        foreach ($this->existing_gallery as $item) {
            $galleryItem = MarinaGallery::find($item['id']);
            if ($galleryItem && $galleryItem->marina_id == $marina->id) {
                $caption = $this->existing_gallery_captions[$item['id']] ?? '';
                $galleryItem->update(['caption' => $caption]);
            }
        }

        // Delete marked gallery images
        foreach ($this->gallery_to_delete as $galleryId) {
            $galleryItem = MarinaGallery::find($galleryId);
            if ($galleryItem && $galleryItem->marina_id == $marina->id) {
                if ($galleryItem->image_path && Storage::disk('public')->exists($galleryItem->image_path)) {
                    Storage::disk('public')->delete($galleryItem->image_path);
                }
                $galleryItem->delete();
            }
        }

        // Get current max order
        $maxOrder = $marina->gallery()->max('order') ?? 0;

        // Save new gallery images
        foreach ($this->gallery_images as $index => $image) {
            if ($image) {
                $path = $image->store('marinas/gallery', 'public');
                $caption = $this->gallery_captions[$index] ?? '';
                MarinaGallery::create([
                    'marina_id' => $marina->id,
                    'image_path' => $path,
                    'caption' => $caption,
                    'category' => 'other',
                    'order' => $maxOrder + $index + 1,
                    'is_primary' => false,
                ]);
            }
        }
    }

    public function getMarinaTypesProperty()
    {
        return MasterData::getMarinaTypes();
    }

    public function render()
    {
        return view('livewire.industry-review.marina-form');
    }
}

