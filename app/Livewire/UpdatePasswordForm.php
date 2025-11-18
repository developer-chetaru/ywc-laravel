<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UpdatePasswordForm extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/])[A-Za-z\d@$!%*?&#^()_+\-=\[\]{};\':"\\|,.<>\/]{8,}$/',
                'confirmed'
            ],
            'password_confirmation' => ['required', 'string'],
        ], [
            'password.regex' => 'Password must contain at least 8 characters with uppercase, lowercase, number, and special character.',
        ]);

        if (! Hash::check($this->current_password, auth()->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        session()->flash('message', 'Password updated successfully!');
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function render()
    {
        return view('livewire.update-password-form')->layout('layouts.app'); 
    }
}
