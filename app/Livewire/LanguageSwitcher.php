<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $locale;

    public function mount()
    {
        $this->locale = Session::get('locale', config('app.locale'));
    }

    public function changeLanguage($locale)
    {
        $this->locale = $locale;
        Session::put('locale', $locale);
        $previousUrl = url()->previous();
        return redirect($previousUrl);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
