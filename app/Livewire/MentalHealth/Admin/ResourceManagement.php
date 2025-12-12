<?php

namespace App\Livewire\MentalHealth\Admin;

use Livewire\Component;
use App\Models\MentalHealthResource;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ResourceManagement extends Component
{
    use WithPagination;

    public function mount()
    {
        // Check if user is super admin
        if (!Auth::user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access');
        }
    }

    public $search = '';
    public $statusFilter = 'all';
    public $categoryFilter = '';
    public $selectedResource = null;
    public $showDetails = false;
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'categoryFilter' => ['except' => ''],
    ];


    public function viewResource($id)
    {
        $this->selectedResource = MentalHealthResource::find($id);
        $this->showDetails = true;
    }

    public function toggleStatus($id)
    {
        $resource = MentalHealthResource::find($id);
        $newStatus = $resource->status === 'published' ? 'draft' : 'published';
        $resource->update(['status' => $newStatus]);
        
        session()->flash('message', "Resource {$newStatus} successfully.");
    }

    public function deleteResource($id)
    {
        $resource = MentalHealthResource::find($id);
        $resource->delete();
        
        session()->flash('message', 'Resource deleted successfully.');
    }

    public function render()
    {
        $query = MentalHealthResource::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => MentalHealthResource::count(),
            'published' => MentalHealthResource::where('status', 'published')->count(),
            'draft' => MentalHealthResource::where('status', 'draft')->count(),
            'total_views' => MentalHealthResource::sum('view_count'),
        ];

        $categories = MentalHealthResource::distinct()->pluck('category')->filter();

        return view('livewire.mental-health.admin.resource-management', [
            'resources' => $resources,
            'stats' => $stats,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
