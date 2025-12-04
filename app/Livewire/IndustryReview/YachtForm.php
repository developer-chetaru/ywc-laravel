<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Yacht;
use App\Models\YachtGallery;
use App\Models\MasterData;
use App\Services\YachtService;

#[Layout('layouts.app')]
class YachtForm extends Component
{
    use WithFileUploads;

    public $yachtId = null;
    public $isEditMode = false;

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

    // Gallery images
    public $gallery_images = [];
    public $gallery_previews = [];
    public $gallery_captions = []; // Captions for new images
    public $existing_gallery = [];
    public $existing_gallery_captions = []; // Captions for existing images
    public $gallery_to_delete = [];

    public $loading = false;
    public $message = '';
    public $error = '';

    public $statuses = [
        'charter' => 'Charter',
        'private' => 'Private',
        'both' => 'Both',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->yachtId = $id;
            $this->isEditMode = true;
            $this->loadYacht($id);
        } else {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            if (!Gate::allows('create', Yacht::class)) {
                session()->flash('error', 'You do not have permission to add yachts.');
                return redirect()->route('industryreview.yachts.manage');
            }

            if ($user->hasRole('Captain')) {
                if (!$user->current_yacht) {
                    session()->flash('error', 'Please set your current yacht in your profile before adding it to the system.');
                    return redirect()->route('industryreview.yachts.manage');
                }
            }
        }
    }

    public function loadYacht($yachtId)
    {
        $this->loading = true;
        try {
            $yacht = Yacht::with('gallery')->findOrFail($yachtId);
            
            if (!Gate::allows('update', $yacht)) {
                session()->flash('error', 'You do not have permission to edit this yacht.');
                return redirect()->route('industryreview.yachts.manage');
            }
            
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
                if (str_starts_with($yacht->cover_image, 'http')) {
                    $this->existing_cover_image = $yacht->cover_image;
                } else {
                    $this->existing_cover_image = asset('storage/' . $yacht->cover_image);
                }
            }

            // Load existing gallery - filter out images without valid paths
            $this->existing_gallery = $yacht->gallery->filter(function ($item) {
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

            $user = Auth::user();

            if ($this->isEditMode) {
                $yacht = Yacht::findOrFail($this->yachtId);
                
                if (!Gate::allows('update', $yacht)) {
                    $this->error = 'You do not have permission to edit this yacht.';
                    $this->loading = false;
                    return;
                }
                
                $service->update($yacht, $data, $this->cover_image);
                
                // Handle gallery images
                $this->saveGalleryImages($yacht);
                
                session()->flash('success', 'Yacht updated successfully!');
                return redirect()->route('industryreview.yachts.manage');
            } else {
                if (!Gate::allows('create', Yacht::class)) {
                    $this->error = 'You do not have permission to add yachts.';
                    $this->loading = false;
                    return;
                }

                if ($user->hasRole('Captain')) {
                    if (!$user->current_yacht || trim($user->current_yacht) !== trim($this->name)) {
                        $this->error = 'As a Captain, you can only add yachts that match your current yacht (' . ($user->current_yacht ?? 'not set') . ').';
                        $this->loading = false;
                        return;
                    }
                }

                $yacht = $service->create($data, $this->cover_image, $user);
                
                // Handle gallery images
                $this->saveGalleryImages($yacht);
                
                session()->flash('success', 'Yacht created successfully!');
                return redirect()->route('industryreview.yachts.manage');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    protected function saveGalleryImages($yacht)
    {
        // Update existing gallery captions
        foreach ($this->existing_gallery as $item) {
            $galleryItem = YachtGallery::find($item['id']);
            if ($galleryItem && $galleryItem->yacht_id == $yacht->id) {
                $caption = $this->existing_gallery_captions[$item['id']] ?? '';
                $galleryItem->update(['caption' => $caption]);
            }
        }

        // Delete marked gallery images
        foreach ($this->gallery_to_delete as $galleryId) {
            $galleryItem = YachtGallery::find($galleryId);
            if ($galleryItem && $galleryItem->yacht_id == $yacht->id) {
                if ($galleryItem->image_path && Storage::disk('public')->exists($galleryItem->image_path)) {
                    Storage::disk('public')->delete($galleryItem->image_path);
                }
                $galleryItem->delete();
            }
        }

        // Get current max order
        $maxOrder = $yacht->gallery()->max('order') ?? 0;

        // Save new gallery images
        foreach ($this->gallery_images as $index => $image) {
            if ($image) {
                $path = $image->store('yachts/gallery', 'public');
                $caption = $this->gallery_captions[$index] ?? '';
                YachtGallery::create([
                    'yacht_id' => $yacht->id,
                    'image_path' => $path,
                    'caption' => $caption,
                    'category' => 'other',
                    'order' => $maxOrder + $index + 1,
                    'is_primary' => false,
                ]);
            }
        }
    }

    public function getYachtTypesProperty()
    {
        return MasterData::where('type', 'yacht_type')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.industry-review.yacht-form');
    }
}

