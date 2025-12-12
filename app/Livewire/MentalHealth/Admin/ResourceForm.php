<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\MentalHealthResource;
use Illuminate\Support\Facades\Auth;

class ResourceForm extends Component
{
    public $resourceId = null;
    public $isEditing = false;

    // Form fields
    public $title = '';
    public $description = '';
    public $category = '';
    public $resource_type = 'article';
    public $content = '';
    public $file_path = '';
    public $thumbnail_path = '';
    public $tags = [];
    public $tagInput = '';
    public $target_audience = ['all_crew'];
    public $reading_time_minutes = 0;
    public $difficulty_level = 'beginner';
    public $author = '';
    public $publication_date = '';
    public $status = 'draft';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'required|string',
        'resource_type' => 'required|string|in:article,video,audio,worksheet,pdf',
        'content' => 'nullable|string',
        'reading_time_minutes' => 'nullable|integer|min:0',
        'difficulty_level' => 'nullable|string|in:beginner,intermediate,advanced',
        'author' => 'nullable|string|max:255',
        'publication_date' => 'nullable|date',
        'status' => 'required|string|in:draft,published,archived',
    ];

    public function mount($id = null)
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }

        if ($id) {
            $this->resourceId = $id;
            $this->isEditing = true;
            $this->loadResource();
        }
    }

    public function loadResource()
    {
        $resource = MentalHealthResource::find($this->resourceId);
        if (!$resource) {
            session()->flash('error', 'Resource not found.');
            return redirect()->route('mental-health.admin.resources');
        }

        $this->title = $resource->title;
        $this->description = $resource->description ?? '';
        $this->category = $resource->category;
        $this->resource_type = $resource->resource_type;
        $this->content = $resource->content ?? '';
        $this->file_path = $resource->file_path ?? '';
        $this->thumbnail_path = $resource->thumbnail_path ?? '';
        $this->tags = $resource->tags ?? [];
        $this->tagInput = implode(', ', $resource->tags ?? []);
        $this->target_audience = $resource->target_audience ?? ['all_crew'];
        $this->reading_time_minutes = $resource->reading_time_minutes ?? 0;
        $this->difficulty_level = $resource->difficulty_level ?? 'beginner';
        $this->author = $resource->author ?? '';
        $this->publication_date = $resource->publication_date ? $resource->publication_date->format('Y-m-d') : '';
        $this->status = $resource->status;
    }

    public function addTag()
    {
        if ($this->tagInput) {
            $newTags = array_map('trim', explode(',', $this->tagInput));
            $this->tags = array_unique(array_merge($this->tags, $newTags));
            $this->tagInput = '';
        }
    }

    public function removeTag($tag)
    {
        $this->tags = array_values(array_filter($this->tags, fn($t) => $t !== $tag));
    }

    public function save()
    {
        $this->validate();

        // Process tags
        if ($this->tagInput) {
            $newTags = array_map('trim', explode(',', $this->tagInput));
            $this->tags = array_unique(array_merge($this->tags, $newTags));
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'resource_type' => $this->resource_type,
            'content' => $this->content,
            'file_path' => $this->file_path,
            'thumbnail_path' => $this->thumbnail_path,
            'tags' => $this->tags,
            'target_audience' => $this->target_audience,
            'reading_time_minutes' => $this->reading_time_minutes,
            'difficulty_level' => $this->difficulty_level,
            'author' => $this->author,
            'publication_date' => $this->publication_date ?: null,
            'status' => $this->status,
        ];

        if ($this->isEditing) {
            $resource = MentalHealthResource::find($this->resourceId);
            $resource->update($data);
            session()->flash('message', 'Resource updated successfully.');
        } else {
            $data['created_by'] = Auth::id();
            MentalHealthResource::create($data);
            session()->flash('message', 'Resource created successfully.');
        }

        return redirect()->route('mental-health.admin.resources');
    }

    public function render()
    {
        return view('livewire.mental-health.admin.resource-form')->layout('layouts.app');
    }
}

