<?php

namespace App\Livewire\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteStop;
use App\Services\Itinerary\RouteBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class RoutePlanner extends Component
{
    use WithFileUploads;

    public string $title = 'Create Route';

    public $coverImage;
    public ?string $coverImagePath = null;

    /** @var array<int, \Illuminate\Http\UploadedFile[]> */
    public array $stopPhotos = [];

    public array $form = [
        'title' => '',
        'description' => '',
        'region' => '',
        'difficulty' => '',
        'season' => '',
        'visibility' => 'private',
        'status' => 'draft',
        'start_date' => null,
        'end_date' => null,
        'tags' => [],
        'notes' => '',
    ];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $stops = [];

    public ?ItineraryRoute $route = null;

    public string $alert = '';

    #[Url(as: 'template')]
    public ?int $template = null;

    public ?int $templateId = null;

    public function mount(): void
    {
        if ($this->template) {
            $this->loadTemplate($this->template);
        } else {
            // Only add blank stops if NOT loading from template
            if (empty($this->stops)) {
                $this->stops = [
                    $this->blankStop(),
                    $this->blankStop(),
                ];
            }
        }
    }
    
    public function updatedTemplate($value): void
    {
        if ($value && $value !== $this->templateId) {
            $this->loadTemplate($value);
        }
    }
    
    protected function loadTemplate(int $templateId): void
    {
        $templateRoute = ItineraryRoute::with('stops')->findOrFail($templateId);
        abort_unless($templateRoute->visibleTo(Auth::user()), 403);

        $this->templateId = $templateRoute->id;
        $this->title = 'Customize Route';
        $this->form = array_merge($this->form, Arr::only($templateRoute->toArray(), [
            'description',
            'region',
            'difficulty',
            'season',
            'visibility',
            'status',
            'start_date',
            'end_date',
        ]));
        $this->form['title'] = 'Copy of '.$templateRoute->title;

        // Clear existing stops first to prevent duplicates
        $this->stops = [];
        
        // Load stops from template - ensure photos are properly handled
        $this->stops = $templateRoute->stops->sortBy('sequence')->values()->map(function (ItineraryRouteStop $stop, int $index) {
            // Ensure photos is an array
            $photos = $stop->photos;
            if (is_string($photos)) {
                $photos = json_decode($photos, true) ?? [];
            }
            if (!is_array($photos)) {
                $photos = [];
            }
            // Filter out empty values
            $photos = array_filter($photos, function($photo) {
                return !empty($photo) && is_string($photo);
            });
            
            return [
                'id' => null,
                'name' => $stop->name,
                'location_label' => $stop->location_label,
                'latitude' => $stop->latitude,
                'longitude' => $stop->longitude,
                'day_number' => $index + 1,
                'sequence' => $index + 1,
                'stay_duration_hours' => $stop->stay_duration_hours,
                'notes' => $stop->notes,
                'photos' => array_values($photos), // Re-index array
            ];
        })->toArray();
        
        if ($templateRoute->cover_image) {
            $this->coverImagePath = $templateRoute->cover_image;
        }
    }

    public function blankStop(): array
    {
        return [
            'id' => null,
            'name' => '',
            'location_label' => '',
            'latitude' => null,
            'longitude' => null,
            'day_number' => count($this->stops) + 1,
            'sequence' => count($this->stops) + 1,
            'stay_duration_hours' => null,
            'notes' => '',
            'photos' => [],
        ];
    }

    public function addStop(): void
    {
        $this->stops[] = $this->blankStop();
        $this->dispatchMapUpdate();
    }

    public function removeStop(int $index): void
    {
        if (count($this->stops) <= 1) {
            return;
        }

        unset($this->stops[$index]);
        $this->stops = array_values(array_map(function ($stop, $idx) {
            $stop['sequence'] = $idx + 1;
            $stop['day_number'] = $idx + 1;
            return $stop;
        }, $this->stops, array_keys($this->stops)));

        $this->dispatchMapUpdate();
    }

    public function updatedStops($value, $key): void
    {
        $this->dispatchMapUpdate();
    }

    protected function dispatchMapUpdate(): void
    {
        $this->dispatch('stops-updated', stops: $this->stops);
    }

    public function updatedCoverImage(): void
    {
        $this->validate(['coverImage' => 'image|max:5120'], [], ['coverImage' => 'cover image']);
    }

    public function removeCoverImage(): void
    {
        if ($this->coverImagePath && Storage::disk('public')->exists($this->coverImagePath)) {
            Storage::disk('public')->delete($this->coverImagePath);
        }
        $this->coverImage = null;
        $this->coverImagePath = null;
    }

    public function updatedStopPhotos($value, $key): void
    {
        // Livewire passes the key in different formats depending on how the property is accessed
        // For wire:model="stopPhotos.0", it might pass "stopPhotos.0" or just "0"
        $stopIndex = null;
        
        \Log::info('updatedStopPhotos called', ['key' => $key, 'value_type' => gettype($value), 'value_count' => is_array($value) ? count($value) : 1]);
        
        if (str_contains($key, '.')) {
            // Format: "stopPhotos.0"
            $parts = explode('.', $key);
            if (count($parts) === 2 && $parts[0] === 'stopPhotos') {
                $stopIndex = (int) $parts[1];
            } else {
                // Try to extract index from other formats
                $lastPart = end($parts);
                if (is_numeric($lastPart)) {
                    $stopIndex = (int) $lastPart;
                } else {
                    \Log::warning('Invalid stopPhotos key format: ' . $key);
                    return;
                }
            }
        } else {
            // Format: just the index "0" - Livewire passes array index directly
            if (is_numeric($key)) {
                $stopIndex = (int) $key;
            } else {
                \Log::warning('Invalid stopPhotos key format (non-numeric): ' . $key);
                return;
            }
        }

        if ($stopIndex === null || !isset($this->stops[$stopIndex])) {
            \Log::warning('Stop index not found', [
                'key' => $key, 
                'stopIndex' => $stopIndex, 
                'stops_count' => count($this->stops),
                'stops_keys' => array_keys($this->stops)
            ]);
            return;
        }
        
        // Ensure value is an array
        if (!is_array($value)) {
            $value = [$value];
        }

        \Log::info('Processing stop photos upload', [
            'stop_index' => $stopIndex,
            'files_count' => count($value),
            'key' => $key
        ]);

        $uploadedPaths = [];
        foreach ($value as $photo) {
            if ($photo instanceof \Illuminate\Http\UploadedFile) {
                try {
                    // Ensure directory exists
                    $directory = storage_path('app/public/route-stops');
                    if (!File::exists($directory)) {
                        File::makeDirectory($directory, 0755, true);
                    }
                    
                    $path = $photo->store('route-stops', 'public');
                    \Log::info('Photo stored', ['path' => $path, 'exists' => Storage::disk('public')->exists($path)]);
                    
                    if ($path && Storage::disk('public')->exists($path)) {
                        $uploadedPaths[] = $path;
                    } else {
                        \Log::error('Photo upload failed - file not found after store', ['path' => $path]);
                    }
                } catch (\Exception $e) {
                    // Log error but continue with other photos
                    \Log::error('Photo upload failed: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::warning('Invalid file type', ['type' => gettype($photo)]);
            }
        }

        if (empty($uploadedPaths)) {
            \Log::warning('No photos were uploaded successfully', ['stop_index' => $stopIndex]);
            return; // No photos were uploaded successfully
        }

        // Initialize photos array if it doesn't exist
        if (!isset($this->stops[$stopIndex]['photos']) || !is_array($this->stops[$stopIndex]['photos'])) {
            $this->stops[$stopIndex]['photos'] = [];
        }

        // Merge new photos with existing ones
        $this->stops[$stopIndex]['photos'] = array_merge(
            $this->stops[$stopIndex]['photos'],
            $uploadedPaths
        );
        
        // Ensure photos array is properly indexed and remove duplicates
        $this->stops[$stopIndex]['photos'] = array_values(array_unique($this->stops[$stopIndex]['photos']));

        \Log::info('Photos updated for stop', [
            'stop_index' => $stopIndex,
            'total_photos' => count($this->stops[$stopIndex]['photos']),
            'paths' => $this->stops[$stopIndex]['photos']
        ]);

        // Clear the uploaded files from the component
        $this->stopPhotos[$stopIndex] = [];
    }

    public function removeStopPhoto($stopIndex, $photoIndex): void
    {
        if (!isset($this->stops[$stopIndex]['photos'][$photoIndex])) {
            return;
        }

        $photoPath = $this->stops[$stopIndex]['photos'][$photoIndex];
        if (Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }

        unset($this->stops[$stopIndex]['photos'][$photoIndex]);
        $this->stops[$stopIndex]['photos'] = array_values($this->stops[$stopIndex]['photos']);
    }

    public function save(RouteBuilder $builder): void
    {
        // Store photos BEFORE validation (they might not be in validated data)
        $photosBackup = [];
        foreach ($this->stops as $index => $stop) {
            $photosBackup[$index] = isset($stop['photos']) && is_array($stop['photos']) 
                ? $stop['photos'] 
                : [];
        }
        
        $validated = $this->validate($this->rules(), [], $this->attributes());

        // Handle cover image upload
        if ($this->coverImage) {
            if ($this->coverImagePath && Storage::disk('public')->exists($this->coverImagePath)) {
                Storage::disk('public')->delete($this->coverImagePath);
            }
            $this->coverImagePath = $this->coverImage->store('route-covers', 'public');
        }

        // Extract form data and flatten form.* keys
        $formData = [];
        foreach ($this->form as $key => $value) {
            $formData[$key] = $validated["form.{$key}"] ?? $value;
        }

        if ($this->coverImagePath) {
            $formData['cover_image'] = $this->coverImagePath;
        }

        // Build stops data - use validated data but preserve photos from backup
        $stopsData = [];
        foreach ($this->stops as $index => $stop) {
            $validatedStop = $validated['stops'][$index] ?? [];
            
            // Start with actual stop data (includes all fields)
            $stopData = $stop;
            
            // Override with validated data (for fields that were validated)
            foreach ($validatedStop as $key => $value) {
                if ($key !== 'photos') {
                    $stopData[$key] = $value;
                }
            }
            
            // CRITICAL: Restore photos from backup (uploaded photos)
            $restoredPhotos = $photosBackup[$index] ?? [];
            
            // Also check if photos exist in current stop (in case backup missed them)
            if (empty($restoredPhotos) && isset($stop['photos']) && is_array($stop['photos'])) {
                $restoredPhotos = $stop['photos'];
            }
            
            $stopData['photos'] = $restoredPhotos;
            
            // Ensure photos array exists and is properly formatted
            if (!is_array($stopData['photos'])) {
                $stopData['photos'] = [];
            }
            
            // Filter out empty photo values and ensure they're strings
            $stopData['photos'] = array_values(array_filter($stopData['photos'], function($photo) {
                return !empty($photo) && is_string($photo);
            }));
            
            $stopsData[$index] = $stopData;
        }

        $payload = array_merge(
            $formData,
            ['stops' => $stopsData]
        );

        // Process stops - use stopsData directly (it already has photos preserved)
        $payload['stops'] = collect($stopsData)
            ->map(function (array $stop, int $index) {
                $stop['sequence'] = $index + 1;
                $stop['day_number'] = $index + 1;
                if (isset($stop['latitude']) && $stop['latitude'] === '') {
                    $stop['latitude'] = null;
                }
                if (isset($stop['longitude']) && $stop['longitude'] === '') {
                    $stop['longitude'] = null;
                }
                
                // Ensure photos array exists and is properly formatted
                if (!isset($stop['photos']) || !is_array($stop['photos'])) {
                    $stop['photos'] = [];
                }
                
                // Filter out empty photo values and ensure they're strings
                $stop['photos'] = array_values(array_filter($stop['photos'], function($photo) {
                    return !empty($photo) && is_string($photo);
                }));
                
                return $stop;
            })
            ->all();

        $route = $builder->createRoute(Auth::user(), $payload);
        $this->route = $route;
        $this->alert = 'Route saved successfully!';

        // Reset cover image after save
        $this->coverImage = null;

        $this->dispatch('route-saved', route: $route);
    }

    public function render()
    {
        return view('livewire.itinerary.route-planner');
    }

    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.description' => ['nullable', 'string', 'max:1000'],
            'form.region' => ['nullable', 'string', 'max:255'],
            'form.difficulty' => ['nullable', 'string', 'max:100'],
            'form.season' => ['nullable', 'string', 'max:100'],
            'form.visibility' => ['required', 'in:public,private,crew'],
            'form.status' => ['required', 'in:draft,active,completed'],
            'form.start_date' => ['nullable', 'date'],
            'form.end_date' => ['nullable', 'date', 'after_or_equal:form.start_date'],
            'coverImage' => ['nullable', 'image', 'max:5120'],
            'stopPhotos.*' => ['nullable', 'array'],
            'stopPhotos.*.*' => ['image', 'max:5120'],
            'stops' => ['required', 'array', 'min:1'],
            'stops.*.name' => ['required', 'string', 'max:255'],
            'stops.*.location_label' => ['nullable', 'string', 'max:255'],
            'stops.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'stops.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'stops.*.stay_duration_hours' => ['nullable', 'integer', 'min:0'],
            'stops.*.notes' => ['nullable', 'string'],
            'stops.*.photos' => ['nullable', 'array'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'form.title' => 'route title',
            'stops.*.name' => 'stop name',
            'stops.*.latitude' => 'latitude',
            'stops.*.longitude' => 'longitude',
        ];
    }
}

