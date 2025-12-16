<?php

namespace App\Livewire\JobBoard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\CrewAvailability;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class AvailableCrewSearch extends Component
{
    use WithPagination;

    #[Url]
    public $position = '';

    #[Url]
    public $location = '';

    #[Url]
    public $radius = 20;

    #[Url]
    public $availableNow = true;

    public $sort = 'distance'; // distance, rating, rate, experience

    public function render()
    {
        $query = CrewAvailability::with('user')
            ->where('status', 'available_now');

        // Filter by position
        if ($this->position) {
            $query->whereJsonContains('available_positions', $this->position);
        }

        // Filter by location/radius (simplified)
        if ($this->location && Auth::user()->latitude && Auth::user()->longitude) {
            // Would need proper distance calculation query
            // For now, just filter by those with location data
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }

        // Filter by availability type
        if ($this->availableNow) {
            $query->where('status', 'available_now');
        }

        $availabilities = $query->paginate(20);

        return view('livewire.job-board.available-crew-search', [
            'availabilities' => $availabilities,
        ]);
    }
}
