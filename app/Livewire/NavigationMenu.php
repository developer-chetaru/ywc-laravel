<?php

namespace App\Livewire;

use Livewire\Component;

class NavigationMenu extends Component
{
    // Explicitly prevent any stops property from being set
    protected $guarded = ['*'];
    
    public function render()
    {
        return view('livewire.navigation-menu');
    }
}
