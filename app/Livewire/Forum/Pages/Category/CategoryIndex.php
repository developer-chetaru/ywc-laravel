<?php

namespace App\Livewire\Forum\Pages\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Component;
use TeamTeaTime\Forum\Events\UserViewingIndex;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class CategoryIndex extends Component
{
    public $categories = [];
    public $selectedThread = null;

    public function mount(Request $request)
    {
        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Once the issue is fixed, this can be removed.
        $this->categories = CategoryAccess::removeParentRelationships($categories);

        if ($request->user() !== null) {
            UserViewingIndex::dispatch($request->user());
        }
    }

    public function loadThread($threadId)
    {
        $this->selectedThread = Thread::find($threadId);
    }

    public function openThread($threadId)
    {
        $this->selectedThread = Thread::find($threadId);
    }

    public function render(): View
    {
        // Calculate total forums (categories)
        $totalForums = Category::count();
        
        // Calculate total threads
        $totalThreads = Thread::count();

        return ViewFactory::make('forum::pages.category.index', [
            'totalForums' => $totalForums,
            'totalThreads' => $totalThreads,
            'selectedThread' => $this->selectedThread,
        ])->layout('forum::layouts.main');
    }
}
