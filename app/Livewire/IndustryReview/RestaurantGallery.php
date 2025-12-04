<?php

namespace App\Livewire\IndustryReview;

use App\Models\Restaurant;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class RestaurantGallery extends Component
{
    public Restaurant $restaurant;

    public function mount($slug)
    {
        $this->restaurant = Restaurant::with(['gallery' => function($query) {
            $query->whereNotNull('image_path')->where('image_path', '!=', '');
        }])->where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.industry-review.restaurant-gallery');
    }
}

