<?php

namespace App\Livewire\Forum;

use App\Services\Forum\ForumSearchService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SearchResults extends Component
{
    use WithPagination;

    public $query = '';
    public $author = '';
    public $categoryId = null;
    public $dateFrom = null;
    public $dateTo = null;
    public $status = 'all'; // 'all', 'open', 'closed'
    public $hasAnswers = null; // null, true, false (can be string from select)
    public $sortBy = 'relevance'; // 'relevance', 'date', 'popularity'
    public $searchIn = 'all'; // 'all', 'threads', 'posts'
    public $showFilters = false;

    public $searchResults = [];
    public $totalResults = 0;
    public $similarThreads = [];

    protected $queryString = [
        'query' => ['except' => ''],
        'author' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'status' => ['except' => 'all'],
        'hasAnswers' => ['except' => ''],
        'sortBy' => ['except' => 'relevance'],
        'searchIn' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->query = request()->get('q', '');
        
        if (!empty($this->query)) {
            $this->performSearch();
        }
    }

    public function updatedQuery()
    {
        $this->resetPage();
        if (!empty($this->query)) {
            $this->performSearch();
        }
    }

    public function updatedAuthor()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedCategoryId()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedStatus()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedHasAnswers()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSearchIn()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function performSearch()
    {
        if (empty($this->query)) {
            $this->searchResults = [];
            $this->totalResults = 0;
            $this->similarThreads = [];
            return;
        }

        $user = Auth::user();
        $searchService = app(ForumSearchService::class);

        $params = [
            'query' => $this->query,
            'author' => $this->author ?: null,
            'category_id' => $this->categoryId ?: null,
            'date_from' => $this->dateFrom ?: null,
            'date_to' => $this->dateTo ?: null,
            'status' => $this->status !== 'all' ? $this->status : null,
            'has_answers' => $this->hasAnswers !== '' && $this->hasAnswers !== null ? (bool) $this->hasAnswers : null,
            'sort_by' => $this->sortBy,
            'search_in' => $this->searchIn,
        ];

        $results = $searchService->search($user, $params);
        
        $this->searchResults = $results;
        $this->totalResults = $results['total_results'];

        // Get similar threads
        if (!empty($this->query)) {
            $this->similarThreads = $searchService->getSimilarThreads($user, $this->query, 5);
        }
    }

    public function clearFilters()
    {
        $this->author = '';
        $this->categoryId = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->status = 'all';
        $this->hasAnswers = null;
        $this->sortBy = 'relevance';
        $this->searchIn = 'all';
        $this->resetPage();
        $this->performSearch();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function truncateContent($content, $length = 200)
    {
        $content = strip_tags($content);
        if (strlen($content) <= $length) {
            return $content;
        }
        return substr($content, 0, $length) . '...';
    }

    public function render()
    {
        // Get categories - forum_categories table uses 'title' column, not 'name'
        $categories = \TeamTeaTime\Forum\Models\Category::query()
            ->orderBy('title')
            ->get();

        return view('livewire.forum.search-results', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
