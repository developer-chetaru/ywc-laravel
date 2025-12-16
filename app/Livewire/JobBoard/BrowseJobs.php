<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\JobPost;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class BrowseJobs extends Component
{
    use WithPagination;

    #[Url]
    public $jobType = ''; // 'permanent', 'temporary', or '' for all

    #[Url]
    public $position = '';

    #[Url]
    public $location = '';

    #[Url]
    public $department = '';

    #[Url]
    public $search = '';

    #[Url]
    public $sort = 'newest'; // newest, salary_high, salary_low, match_score

    public $salaryMin = '';
    public $salaryMax = '';

    public function updated($property)
    {
        if (in_array($property, ['jobType', 'position', 'location', 'department', 'search', 'sort'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = JobPost::published();

        // Filter by job type
        if ($this->jobType) {
            $query->where('job_type', $this->jobType);
        }

        // Filter by position
        if ($this->position) {
            $query->where('position_title', 'like', '%' . $this->position . '%')
                ->orWhere('position_level', 'like', '%' . $this->position . '%');
        }

        // Filter by location
        if ($this->location) {
            $query->where('location', 'like', '%' . $this->location . '%');
        }

        // Filter by department
        if ($this->department) {
            $query->where('department', $this->department);
        }

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('position_title', 'like', '%' . $this->search . '%')
                    ->orWhere('about_position', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        // Salary filter
        if ($this->salaryMin) {
            $query->where(function($q) {
                $q->where('salary_max', '>=', $this->salaryMin)
                    ->orWhere('salary_min', '>=', $this->salaryMin);
            });
        }
        if ($this->salaryMax) {
            $query->where(function($q) {
                $q->where('salary_min', '<=', $this->salaryMax)
                    ->orWhere('salary_max', '<=', $this->salaryMax);
            });
        }

        // Sorting
        switch ($this->sort) {
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('published_at');
                break;
        }

        $jobs = $query->with(['user', 'yacht'])->paginate(12);

        // Get saved job IDs for current user
        $savedJobIds = Auth::check() 
            ? Auth::user()->savedJobPosts()->pluck('job_post_id')->toArray()
            : [];

        return view('livewire.job-board.browse-jobs', [
            'jobs' => $jobs,
            'savedJobIds' => $savedJobIds,
        ]);
    }

    public function clearFilters()
    {
        $this->jobType = '';
        $this->position = '';
        $this->location = '';
        $this->department = '';
        $this->search = '';
        $this->salaryMin = '';
        $this->salaryMax = '';
        $this->sort = 'newest';
        $this->resetPage();
    }
}
