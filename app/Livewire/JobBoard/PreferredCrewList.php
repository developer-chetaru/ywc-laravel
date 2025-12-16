<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\PreferredCrewList as PreferredCrewListModel;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class PreferredCrewList extends Component
{
    public $preferredCrew = [];

    public function mount()
    {
        $this->loadPreferredCrew();
    }

    public function loadPreferredCrew()
    {
        $this->preferredCrew = PreferredCrewListModel::with('crew')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function removeFromList($id)
    {
        PreferredCrewListModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();
        
        $this->loadPreferredCrew();
        session()->flash('success', 'Removed from preferred list');
    }

    public function render()
    {
        return view('livewire.job-board.preferred-crew-list');
    }
}
