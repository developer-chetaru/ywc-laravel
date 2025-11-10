<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShareProfileMail;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class ProfileController extends Controller
{
    public function share(Request $request)
    {
        $request->validate([
            'profile_emails' => 'required|string',
            'profile_message' => 'nullable|string',
        ]);

        $emails = explode(',', $request->profile_emails);
        $message = $request->profile_message;

        foreach ($emails as $email) {
            Mail::to(trim($email))->send(new ShareProfileMail(auth()->user(), $message));
        }

        return response()->json(['success' => true]);
    }

	public function show($encryptedId)
    {
        try {

            $userId = Crypt::decryptString(urldecode($encryptedId));

            $user = User::with('documents')->findOrFail($userId);

            return view('profile.visit-profile', compact('user'));

        } catch (\Exception $e) {
            abort(404, 'Profile not found or link is invalid.');
        }
    }

	public function showPublic($encryptedId)
    {
        try {
            $userId = Crypt::decryptString($encryptedId);
            $user = User::with('documents')->findOrFail($userId);
            return view('profile.visit-profile', compact('user'));
        } catch (\Exception $e) {
            abort(404, 'Invalid or expired QR profile link.');
        }
    }
  
  	public function showOld($encryptedId)
    {
        try {
            // Decrypt the ID
            $userId = Crypt::decrypt($encryptedId);

            // Fetch user and documents
            $user = User::with('documents')->findOrFail($userId);

            return view('profile.visit-profile', compact('user'));
        } catch (\Exception $e) {
            abort(404, 'Profile not found or link is invalid.');
        }
    }

	public function profile(Request $request)
    {
        try {
            $user = auth()->user();

            return response()->json([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'image' => $user->profile_photo_path ? url('uploads/profile/' . $user->profile_photo_path) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user profile'], 500);
        }
    }

    /**
     * Update the authenticated user's profile details and image.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
 	public function updateProfile(Request $request)
  {
      try {
          $user = auth()->user();

          $user->first_name     = $request->first_name;
          $user->last_name      = $request->last_name;
          $user->email          = $request->email ?? $user->email;
          $user->dob            = $request->dob ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d') : $user->dob;
          $user->phone          = $request->phone;
          $user->gender         = $request->gender;
          $user->nationality    = $request->nationality;
          $user->marital_status = $request->marital_status;
          $user->birth_country  = $request->birth_country;
          $user->birth_province = $request->birth_province;

          // Image upload (if provided)
          if ($request->hasFile('image')) {
              $image = $request->file('image');
              $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
              $image->move(public_path('uploads/profile'), $imageName);
              $user->profile_photo_path = $imageName;
          }

          // Check if all required fields are present, then mark status = true
          if (
              $user->first_name &&
              $user->last_name &&
              $user->email &&
              $user->dob &&
              $user->phone &&
              $user->gender &&
              $user->nationality &&
              $user->marital_status &&
              $user->birth_country &&
              $user->birth_province &&
              $user->profile_photo_path // Optional: include image if you want it mandatory
          ) {
              $user->status = true;
          }

          $user->save();

          return response()->json([
              'status'  => true,
              'message' => 'Profile updated successfully'
          ]);
      } catch (\Exception $e) {
          return response()->json([
              'status'  => false,
              'error'   => 'Update failed',
              'details' => $e->getMessage()
          ], 500);
      }
  }
}
