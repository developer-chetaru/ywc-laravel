<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Yacht;
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
            $yacht = Yacht::findOrFail($yachtId);
            
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

                $service->create($data, $this->cover_image, $user);
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

