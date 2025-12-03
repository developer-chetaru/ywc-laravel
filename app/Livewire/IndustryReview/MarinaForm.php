<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Marina;
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
            $marina = Marina::findOrFail($marinaId);
            
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
                $this->existing_cover_image = Storage::disk('public')->url($marina->cover_image);
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
                session()->flash('success', 'Marina updated successfully!');
                return redirect()->route('industryreview.marinas.manage');
            } else {
                $service->create($data, $this->cover_image);
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

    public function getMarinaTypesProperty()
    {
        return MasterData::getMarinaTypes();
    }

    public function render()
    {
        return view('livewire.industry-review.marina-form');
    }
}

