<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Itinerary;

class ItinerarySystem extends Component
{
    // public $itineraries;
    // public $title, $description, $start_date, $end_date, $itinerary_id;
    // public $updateMode = false;

    // protected $rules = [
    //     'title' => 'required|string|max:255',
    //     'description' => 'nullable|string',
    //     'start_date' => 'required|date',
    //     'end_date' => 'required|date|after_or_equal:start_date',
    // ];

    // public function mount()
    // {
    //     $this->loadItineraries();
    // }

    // public function loadItineraries()
    // {
    //     $this->itineraries = Itinerary::latest()->get();
    // }

    // public function resetInput()
    // {
    //     $this->title = '';
    //     $this->description = '';
    //     $this->start_date = '';
    //     $this->end_date = '';
    //     $this->itinerary_id = null;
    //     $this->updateMode = false;
    // }

    // public function store()
    // {
    //     $this->validate();

    //     Itinerary::create([
    //         'title' => $this->title,
    //         'description' => $this->description,
    //         'start_date' => $this->start_date,
    //         'end_date' => $this->end_date,
    //     ]);

    //     $this->resetInput();
    //     $this->loadItineraries();
    //     session()->flash('message', 'Itinerary created successfully.');
    // }

    // public function edit($id)
    // {
    //     $itinerary = Itinerary::findOrFail($id);
    //     $this->itinerary_id = $id;
    //     $this->title = $itinerary->title;
    //     $this->description = $itinerary->description;
    //     $this->start_date = $itinerary->start_date;
    //     $this->end_date = $itinerary->end_date;
    //     $this->updateMode = true;
    // }

    // public function update()
    // {
    //     $this->validate();

    //     if ($this->itinerary_id) {
    //         $itinerary = Itinerary::find($this->itinerary_id);
    //         $itinerary->update([
    //             'title' => $this->title,
    //             'description' => $this->description,
    //             'start_date' => $this->start_date,
    //             'end_date' => $this->end_date,
    //         ]);

    //         $this->resetInput();
    //         $this->loadItineraries();
    //         session()->flash('message', 'Itinerary updated successfully.');
    //     }
    // }

    // public function delete($id)
    // {
    //     Itinerary::find($id)->delete();
    //     $this->loadItineraries();
    //     session()->flash('message', 'Itinerary deleted successfully.');
    // }

    public function render()
    {
        return view('livewire.itinerary-system')->layout('layouts.app');
    }
}
