<?php

namespace App\View\Composers;

use Illuminate\View\View;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class CategoryIndexComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        
        // Always ensure selectedThread is defined, defaulting to null if not set
        // This prevents "Undefined variable" errors in the view
        // Check if selectedThread exists in the data, preserve it if it does, otherwise set to null
        $selectedThread = array_key_exists('selectedThread', $data) ? $data['selectedThread'] : null;
        
        $view->with([
            'totalForums' => Category::count(),
            'totalThreads' => Thread::count(),
            'selectedThread' => $selectedThread,
        ]);
    }
}
