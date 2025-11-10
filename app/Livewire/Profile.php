<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public $photo;
    public $profile_photo_path;
    public $first_name, $last_name, $email;
    public $user;

    public function mount()
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->profile_photo_path = $user->profile_photo_path;
    }

    public function updateProfile()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
        ]);

        session()->flash('profile-message', 'Profile updated successfully.');
    }

    public function updateProfilePhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $this->photo->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $path,
        ]);

        // 🔑 Update Livewire state
        $this->profile_photo_path = $path;
        $this->photo = null;

        session()->flash('message', 'Profile photo updated successfully.');
    }

    public function removeProfilePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update([
            'profile_photo_path' => null,
        ]);

        // 🔑 Update Livewire state
        $this->profile_photo_path = null;

        session()->flash('profile-message', 'Profile photo removed.');
}

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app'); 
    }
}
