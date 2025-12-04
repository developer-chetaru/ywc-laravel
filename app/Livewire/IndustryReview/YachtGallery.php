<?php

namespace App\Livewire\IndustryReview;

use App\Models\Yacht;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class YachtGallery extends Component
{
    public Yacht $yacht;

    public function mount($slug)
    {
        $this->yacht = Yacht::with(['gallery' => function($query) {
            $query->whereNotNull('image_path')->where('image_path', '!=', '');
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.industry-review.yacht-gallery');
    }
}

