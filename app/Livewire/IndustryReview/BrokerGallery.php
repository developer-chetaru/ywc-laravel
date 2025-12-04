<?php

namespace App\Livewire\IndustryReview;

use App\Models\Broker;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class BrokerGallery extends Component
{
    public Broker $broker;

    public function mount($slug)
    {
        $this->broker = Broker::with(['gallery' => function($query) {
            $query->whereNotNull('image_path')->where('image_path', '!=', '');
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.industry-review.broker-gallery');
    }
}

