<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Contractor;
use App\Models\ContractorGallery;
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
    public $price_range = null;
    public $logo;
    public $logo_preview = null;
    public $existing_logo = null;

    // Gallery images
    public $gallery_images = [];
    public $gallery_previews = [];
    public $gallery_captions = []; // Captions for new images
    public $existing_gallery = [];
    public $existing_gallery_captions = []; // Captions for existing images
    public $gallery_to_delete = [];

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
            $contractor = Contractor::with('gallery')->findOrFail($contractorId);
            
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
            
            if ($contractor->logo) {
                if (str_starts_with($contractor->logo, 'http')) {
                    $this->existing_logo = $contractor->logo;
                } else {
                    $this->existing_logo = asset('storage/' . $contractor->logo);
                }
            }

            // Load existing gallery - filter out images without valid paths
            $this->existing_gallery = $contractor->gallery->filter(function ($item) {
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

    public function updatedLogo()
    {
        if ($this->logo) {
            $this->logo_preview = $this->logo->temporaryUrl();
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
                
                // Handle gallery images
                $this->saveGalleryImages($contractor);
                
                session()->flash('success', 'Contractor updated successfully!');
                return redirect()->route('industryreview.contractors.manage');
            } else {
                $data['slug'] = Str::slug($this->name);
                $contractor = Contractor::create($data);
                
                // Handle gallery images
                $this->saveGalleryImages($contractor);
                
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

    protected function saveGalleryImages($contractor)
    {
        // Update existing gallery captions
        foreach ($this->existing_gallery as $item) {
            $galleryItem = ContractorGallery::find($item['id']);
            if ($galleryItem && $galleryItem->contractor_id == $contractor->id) {
                $caption = $this->existing_gallery_captions[$item['id']] ?? '';
                $galleryItem->update(['caption' => $caption]);
            }
        }

        // Delete marked gallery images
        foreach ($this->gallery_to_delete as $galleryId) {
            $galleryItem = ContractorGallery::find($galleryId);
            if ($galleryItem && $galleryItem->contractor_id == $contractor->id) {
                if ($galleryItem->image_path && Storage::disk('public')->exists($galleryItem->image_path)) {
                    Storage::disk('public')->delete($galleryItem->image_path);
                }
                $galleryItem->delete();
            }
        }

        // Get current max order
        $maxOrder = $contractor->gallery()->max('order') ?? 0;

        // Save new gallery images
        foreach ($this->gallery_images as $index => $image) {
            if ($image) {
                $path = $image->store('contractors/gallery', 'public');
                $caption = $this->gallery_captions[$index] ?? '';
                ContractorGallery::create([
                    'contractor_id' => $contractor->id,
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
        return view('livewire.industry-review.contractor-form');
    }
}

