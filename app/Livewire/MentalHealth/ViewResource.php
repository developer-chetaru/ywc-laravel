<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthResource;

class ViewResource extends Component
{
    public $resourceId;
    public $resource;

    public function mount($id)
    {
        $this->resourceId = $id;
        $this->loadResource();
    }

    public function loadResource()
    {
        $this->resource = MentalHealthResource::where('id', $this->resourceId)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment view count
        $this->resource->increment('view_count');
    }

    public function render()
    {
        return view('livewire.mental-health.view-resource')
            ->layout('layouts.app');
    }
}

