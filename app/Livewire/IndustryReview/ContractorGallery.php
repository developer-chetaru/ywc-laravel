<?php

namespace App\Livewire\IndustryReview;

use App\Models\Contractor;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ContractorGallery extends Component
{
    public Contractor $contractor;

    public function mount($slug)
    {
        $this->contractor = Contractor::with(['gallery' => function($query) {
            $query->whereNotNull('image_path')->where('image_path', '!=', '');
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.industry-review.contractor-gallery');
    }
}

