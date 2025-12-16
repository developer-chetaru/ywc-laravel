<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfilePhotoController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo using standard Laravel upload (works without tmpfile())
        $file = $request->file('photo');
        $path = $file->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $path,
        ]);

        // Redirect back to profile page with success message
        return redirect()->route('profile')->with('profile-message', 'Profile photo updated successfully.');
    }

    public function remove()
    {
        $user = Auth::user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->update([
            'profile_photo_path' => null,
        ]);

        // Redirect back to profile page with success message
        return redirect()->route('profile')->with('profile-message', 'Profile photo removed.');
    }
}

