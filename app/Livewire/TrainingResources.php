<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

class TrainingResources extends Component
{
    public function mount()
    {
        // Redirect to the new training courses page
        return Redirect::route('training.courses');
    }

    public function render()
    {
        return view('livewire.training-resources')->layout('layouts.app');
    }
}
