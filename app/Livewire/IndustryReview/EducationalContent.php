<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EducationalContent extends Component
{
    public $activeSection = 'brokers';

    public function setSection($section)
    {
        $this->activeSection = $section;
    }

    public function render()
    {
        return view('livewire.industry-review.educational-content');
    }
}
