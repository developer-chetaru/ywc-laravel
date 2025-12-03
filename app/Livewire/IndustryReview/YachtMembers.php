<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Yacht;
use App\Models\User;

#[Layout('layouts.app')]
class YachtMembers extends Component
{
    public $yachtId;
    public $yacht;
    public $members = [];

    public function mount($id)
    {
        $this->yachtId = $id;
        $this->yacht = Yacht::findOrFail($id);
        
        // Get users with this yacht as current_yacht
        $this->members = User::where('current_yacht', $this->yacht->name)
            ->with('roles')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'profile_photo_path' => $user->profile_photo_path,
                    'roles' => $user->getRoleNames()->toArray(),
                    'current_yacht_start_date' => $user->current_yacht_start_date,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.industry-review.yacht-members');
    }
}

