<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Broker;
use App\Models\BrokerGallery;
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
    public $fee_structure = null;
    public $regions_servedInput = '';
    public $years_in_business = '';
    public $is_myba_member = false;
    public $is_licensed = false;
    public $certificationsInput = '';
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
            $broker = Broker::with('gallery')->findOrFail($brokerId);
            
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
            
            if ($broker->logo) {
                if (str_starts_with($broker->logo, 'http')) {
                    $this->existing_logo = $broker->logo;
                } else {
                    $this->existing_logo = asset('storage/' . $broker->logo);
                }
            }

            // Load existing gallery - filter out images without valid paths
            $this->existing_gallery = $broker->gallery->filter(function ($item) {
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
                'type' => $this->type,
                'description' => $this->description,
                'primary_location' => $this->primary_location,
                'office_locations' => !empty($this->office_locationsInput) ? array_map('trim', explode(',', $this->office_locationsInput)) : [],
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'specialties' => !empty($this->specialtiesInput) ? array_map('trim', explode(',', $this->specialtiesInput)) : [],
                'fee_structure' => !empty($this->fee_structure) ? $this->fee_structure : null,
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
                
                // Handle gallery images
                $this->saveGalleryImages($broker);
                
                session()->flash('success', 'Broker updated successfully!');
                return redirect()->route('industryreview.brokers.manage');
            } else {
                $data['slug'] = Str::slug($this->name);
                $broker = Broker::create($data);
                
                // Handle gallery images
                $this->saveGalleryImages($broker);
                
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

    protected function saveGalleryImages($broker)
    {
        // Update existing gallery captions
        foreach ($this->existing_gallery as $item) {
            $galleryItem = BrokerGallery::find($item['id']);
            if ($galleryItem && $galleryItem->broker_id == $broker->id) {
                $caption = $this->existing_gallery_captions[$item['id']] ?? '';
                $galleryItem->update(['caption' => $caption]);
            }
        }

        // Delete marked gallery images
        foreach ($this->gallery_to_delete as $galleryId) {
            $galleryItem = BrokerGallery::find($galleryId);
            if ($galleryItem && $galleryItem->broker_id == $broker->id) {
                if ($galleryItem->image_path && Storage::disk('public')->exists($galleryItem->image_path)) {
                    Storage::disk('public')->delete($galleryItem->image_path);
                }
                $galleryItem->delete();
            }
        }

        // Get current max order
        $maxOrder = $broker->gallery()->max('order') ?? 0;

        // Save new gallery images
        foreach ($this->gallery_images as $index => $image) {
            if ($image) {
                $path = $image->store('brokers/gallery', 'public');
                $caption = $this->gallery_captions[$index] ?? '';
                BrokerGallery::create([
                    'broker_id' => $broker->id,
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
        return view('livewire.industry-review.broker-form');
    }
}

