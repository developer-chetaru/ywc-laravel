<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;

class YachtCreate extends Component
{
    use WithFileUploads;

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

    public $loading = false;
    public $message = '';
    public $error = '';

    public $types = [
        'motor_yacht' => 'Motor Yacht',
        'sailing_yacht' => 'Sailing Yacht',
        'explorer' => 'Explorer',
        'catamaran' => 'Catamaran',
        'other' => 'Other',
    ];

    public $statuses = [
        'charter' => 'Charter',
        'private' => 'Private',
        'both' => 'Both',
    ];

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
            // Use Sanctum token for authenticated requests
            $user = auth()->user();
            if (!$user) {
                $this->error = 'You must be logged in to create a yacht.';
                $this->loading = false;
                return;
            }

            // Create a temporary token for this request
            $token = $user->createToken('yacht-create')->plainTextToken;

            // Build form data
            $formData = [
                ['name' => 'name', 'contents' => $this->name],
                ['name' => 'type', 'contents' => $this->type],
                ['name' => 'status', 'contents' => $this->status],
            ];

            if ($this->length_meters) $formData[] = ['name' => 'length_meters', 'contents' => $this->length_meters];
            if ($this->length_feet) $formData[] = ['name' => 'length_feet', 'contents' => $this->length_feet];
            if ($this->year_built) $formData[] = ['name' => 'year_built', 'contents' => $this->year_built];
            if ($this->flag_registry) $formData[] = ['name' => 'flag_registry', 'contents' => $this->flag_registry];
            if ($this->home_port) $formData[] = ['name' => 'home_port', 'contents' => $this->home_port];
            if ($this->crew_capacity) $formData[] = ['name' => 'crew_capacity', 'contents' => $this->crew_capacity];
            if ($this->guest_capacity) $formData[] = ['name' => 'guest_capacity', 'contents' => $this->guest_capacity];

            // Add file if exists
            if ($this->cover_image) {
                $formData[] = [
                    'name' => 'cover_image',
                    'contents' => fopen($this->cover_image->getRealPath(), 'r'),
                    'filename' => $this->cover_image->getClientOriginalName(),
                ];
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->asMultipart()->post(url('/api/yachts'), $formData);

            // Revoke the temporary token
            $user->tokens()->where('name', 'yacht-create')->delete();

            if ($response->successful()) {
                $this->message = 'Yacht created successfully!';
                $this->resetForm();
                $this->dispatch('yacht-created');
            } else {
                $errors = $response->json('errors', []);
                $this->error = $response->json('message', 'Failed to create yacht. Please check the form.');
                if (!empty($errors)) {
                    $this->error = collect($errors)->flatten()->implode(', ');
                }
            }
        } catch (\Exception $e) {
            $this->error = 'An error occurred: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->type = '';
        $this->length_meters = '';
        $this->length_feet = '';
        $this->year_built = '';
        $this->flag_registry = '';
        $this->home_port = '';
        $this->crew_capacity = '';
        $this->guest_capacity = '';
        $this->status = 'charter';
        $this->cover_image = null;
        $this->cover_image_preview = null;
    }

    public function render()
    {
        return view('livewire.industry-review.yacht-create');
    }
}

