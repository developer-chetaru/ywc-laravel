<?php

namespace App\Livewire\Forum;

use Livewire\Component;

class UserCategoryIndex extends Component
{
    public function render()
    {
        $categories = Category::where('user_id', auth()->id())->get();

        return view('livewire.forum.user-category-index', [
            'categories' => $categories,
        ]);

        // return view('livewire.forum.user-category-index');
    }
}
