<?php

namespace App\Livewire;

use Livewire\Component;
// use App\Models\LegalSupport;

class LegalSupport extends Component
{
    // public $name, $email, $subject, $message;
    // public $successMessage = '';

    // protected $rules = [
    //     'name' => 'required|string|min:3|max:100',
    //     'email' => 'required|email',
    //     'subject' => 'required|string|min:3|max:150',
    //     'message' => 'required|string|min:5|max:1000',
    // ];

    // public function submit()
    // {
    //     $this->validate();

    //     LegalSupport::create([
    //         'name'    => $this->name,
    //         'email'   => $this->email,
    //         'subject' => $this->subject,
    //         'message' => $this->message,
    //     ]);

    //     $this->reset(['name', 'email', 'subject', 'message']);
    //     $this->successMessage = 'Your legal support request has been submitted successfully!';
    // }

    public function render()
    {
        return view('livewire.legal-support')->layout('layouts.app');
    }
}
