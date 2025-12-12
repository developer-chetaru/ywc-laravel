<?php

namespace App\Livewire\MentalHealth;

use Livewire\Component;
use App\Models\MentalHealthResource;
use Livewire\WithPagination;

class ResourcesLibrary extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $resourceType = '';
    public $difficultyLevel = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'resourceType' => ['except' => ''],
    ];

    public function render()
    {
        $query = MentalHealthResource::where('status', 'published');

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereJsonContains('tags', $this->search);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->resourceType) {
            $query->where('resource_type', $this->resourceType);
        }

        if ($this->difficultyLevel) {
            $query->where('difficulty_level', $this->difficultyLevel);
        }

        $resources = $query->orderBy('view_count', 'desc')->paginate(12);

        return view('livewire.mental-health.resources-library', [
            'resources' => $resources,
        ])->layout('layouts.app');
    }
}
