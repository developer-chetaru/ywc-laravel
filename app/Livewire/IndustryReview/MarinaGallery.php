<?php

namespace App\Livewire\IndustryReview;

use App\Models\Marina;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MarinaGallery extends Component
{
    public Marina $marina;

    public function mount($slug)
    {
        $this->marina = Marina::with(['gallery' => function($query) {
            $query->whereNotNull('image_path')->where('image_path', '!=', '');
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.industry-review.marina-gallery');
    }
}

