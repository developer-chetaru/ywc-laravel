<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use Livewire\WithPagination;

class TrainingResources extends Component
{
    // use WithPagination;

    // public $title, $description, $resource_link, $trainingId;
    // public $updateMode = false;

    // protected $rules = [
    //     'title' => 'required|string|max:255',
    //     'description' => 'nullable|string',
    //     'resource_link' => 'nullable|url',
    // ];

    public function render()
    {
        return view('livewire.training-resources')->layout('layouts.app');
    }

    // public function render()
    // {
    //     return view('livewire.training-resources', [
    //         'trainings' => Training::latest()->paginate(5),
    //     ]);
    // }

    // public function resetInputFields()
    // {
    //     $this->title = '';
    //     $this->description = '';
    //     $this->resource_link = '';
    //     $this->trainingId = null;
    // }

    // public function store()
    // {
    //     $this->validate();

    //     Training::create([
    //         'title' => $this->title,
    //         'description' => $this->description,
    //         'resource_link' => $this->resource_link,
    //     ]);

    //     session()->flash('message', 'Training added successfully.');

    //     $this->resetInputFields();
    // }

    // public function edit($id)
    // {
    //     $training = Training::findOrFail($id);
    //     $this->trainingId = $id;
    //     $this->title = $training->title;
    //     $this->description = $training->description;
    //     $this->resource_link = $training->resource_link;
    //     $this->updateMode = true;
    // }

    // public function update()
    // {
    //     $this->validate();

    //     if ($this->trainingId) {
    //         $training = Training::find($this->trainingId);
    //         $training->update([
    //             'title' => $this->title,
    //             'description' => $this->description,
    //             'resource_link' => $this->resource_link,
    //         ]);

    //         session()->flash('message', 'Training updated successfully.');
    //         $this->resetInputFields();
    //         $this->updateMode = false;
    //     }
    // }

    // public function delete($id)
    // {
    //     Training::find($id)->delete();
    //     session()->flash('message', 'Training deleted successfully.');
    // }

    // public function cancel()
    // {
    //     $this->resetInputFields();
    //     $this->updateMode = false;
    // }
}
