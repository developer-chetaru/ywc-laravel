<?php

namespace App\Livewire\Marketplace;

use Livewire\Component;

class ItineraryIndex extends Component
{
    public function render()
    {
        return view('livewire.Itinerary.itinerary-index')->layout('layouts.app');
    }
}