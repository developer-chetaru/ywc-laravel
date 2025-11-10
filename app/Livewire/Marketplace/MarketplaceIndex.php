<?php

namespace App\Livewire\Marketplace;

use Livewire\Component;

class MarketplaceIndex extends Component
{
    public function render()
    {
        return view('livewire.marketplace.marketplace-index')->layout('layouts.app');
    }
}


// <?php

// namespace App\Livewire\Marketplace;

// use Livewire\Component;
// use App\Models\Product;

// class MarketplaceIndex extends Component
// {
//     public $search = '';

//     public function render()
//     {
//         $products = Product::query()
//             ->when($this->search, fn($q) =>
//                 $q->where('name', 'like', '%' . $this->search . '%')
//             )
//             ->orderBy('created_at', 'desc')
//             ->get();

//         return view('livewire.marketplace.marketplace-index', [
//             'products' => $products,
//         ]);
//     }
// }