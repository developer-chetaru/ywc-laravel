<?php

namespace App\Livewire;

use Livewire\Component;

class PurchaseHistory extends Component
{
    public function render()
    {
        return view('livewire.purchase-history')->layout('layouts.app');
    }
}

