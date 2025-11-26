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

            // Format dob from Y-m-d to d/m/Y if it exists
            $dob = null;
            if ($user->dob) {
                try {
                    $dob = \Carbon\Carbon::createFromFormat('Y-m-d', $user->dob)->format('d/m/Y');
                } catch (\Exception $e) {
                    $dob = $user->dob;
                }
            }

            // Format current_yacht_start_date if it exists
            $currentYachtStartDate = null;
            if ($user->current_yacht_start_date) {
                try {
                    $currentYachtStartDate = \Carbon\Carbon::createFromFormat('Y-m-d', $user->current_yacht_start_date)->format('m/d/Y');
                } catch (\Exception $e) {
                    $currentYachtStartDate = $user->current_yacht_start_date;
                }
            }

            // Format previous yachts dates
            $previousYachts = [];
            if ($user->previous_yachts) {
                foreach ($user->previous_yachts as $yacht) {
                    $formattedYacht = [
                        'yacht_id' => $yacht['yacht_id'] ?? null,
                        'name' => $yacht['name'] ?? '',
                        'start_date' => null,
                        'end_date' => null,
                    ];
                    
                    if (!empty($yacht['start_date'])) {
                        try {
                            $formattedYacht['start_date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $yacht['start_date'])->format('m/d/Y');
                        } catch (\Exception $e) {
                            $formattedYacht['start_date'] = $yacht['start_date'];
                        }
                    }
                    
                    if (!empty($yacht['end_date'])) {
                        try {
                            $formattedYacht['end_date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $yacht['end_date'])->format('m/d/Y');
                        } catch (\Exception $e) {
                            $formattedYacht['end_date'] = $yacht['end_date'];
                        }
                    }
                    
                    $previousYachts[] = $formattedYacht;
                }
            }

            return response()->json([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'dob' => $dob,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'nationality' => $user->nationality,
                'marital_status' => $user->marital_status,
                'birth_country' => $user->birth_country,
                'birth_province' => $user->birth_province,
                'user_id' => $user->id,
                'image' => $user->profile_photo_path ? url('uploads/profile/' . $user->profile_photo_path) : null,
                // Crew Profile Fields
                'years_experience' => $user->years_experience,
                'current_yacht' => $user->current_yacht,
                'current_yacht_start_date' => $currentYachtStartDate,
                'sea_service_time_months' => $user->sea_service_time_months,
                'availability_status' => $user->availability_status,
                'availability_message' => $user->availability_message,
                'looking_to_meet' => $user->looking_to_meet ?? false,
                'looking_for_work' => $user->looking_for_work ?? false,
                'languages' => $user->languages ?? [],
                'certifications' => $user->certifications ?? [],
                'specializations' => $user->specializations ?? [],
                'interests' => $user->interests ?? [],
                'previous_yachts' => $previousYachts,
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

          // Validate request
          $request->validate([
              'first_name' => 'nullable|string|max:255',
              'last_name' => 'nullable|string|max:255',
              'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
              'dob' => 'nullable|date_format:d/m/Y',
              'phone' => 'nullable|string|max:255',
              'gender' => 'nullable|string|max:255',
              'nationality' => 'nullable|string|max:255',
              'marital_status' => 'nullable|string|max:255',
              'birth_country' => 'nullable|string|max:255',
              'birth_province' => 'nullable|string|max:255',
              // Crew Profile Fields
              'years_experience' => 'nullable|integer|min:0|max:100',
              'current_yacht' => 'nullable|string|max:255',
              'current_yacht_start_date' => 'nullable|date_format:m/d/Y',
              'sea_service_time_months' => 'nullable|integer|min:0',
              'availability_status' => 'nullable|in:available,busy,looking_for_work,on_leave',
              'availability_message' => 'nullable|string|max:500',
              'looking_to_meet' => 'nullable|boolean',
              'looking_for_work' => 'nullable|boolean',
              'languages' => 'nullable|array',
              'languages.*' => 'nullable|string|max:255',
              'certifications' => 'nullable|array',
              'certifications.*' => 'nullable|string|max:255',
              'specializations' => 'nullable|array',
              'specializations.*' => 'nullable|string|max:255',
              'interests' => 'nullable|array',
              'interests.*' => 'nullable|string|max:255',
              'previous_yachts' => 'nullable|array',
              'previous_yachts.*.yacht_id' => 'nullable|integer|exists:yachts,id',
              'previous_yachts.*.name' => 'required_with:previous_yachts|string|max:255',
              'previous_yachts.*.start_date' => 'nullable|date_format:m/d/Y',
              'previous_yachts.*.end_date' => 'nullable|date_format:m/d/Y',
              'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);

          // Update basic profile fields
          if ($request->has('first_name')) {
              $user->first_name = $request->first_name;
          }
          if ($request->has('last_name')) {
              $user->last_name = $request->last_name;
          }
          if ($request->has('email')) {
              $user->email = $request->email;
          }
          if ($request->has('dob')) {
              $user->dob = \Carbon\Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d');
          }
          if ($request->has('phone')) {
              $user->phone = $request->phone;
          }
          if ($request->has('gender')) {
              $user->gender = $request->gender;
          }
          if ($request->has('nationality')) {
              $user->nationality = $request->nationality;
          }
          if ($request->has('marital_status')) {
              $user->marital_status = $request->marital_status;
          }
          if ($request->has('birth_country')) {
              $user->birth_country = $request->birth_country;
          }
          if ($request->has('birth_province')) {
              $user->birth_province = $request->birth_province;
          }

          // Update crew profile fields
          if ($request->has('years_experience')) {
              $user->years_experience = $request->years_experience;
          }
          if ($request->has('current_yacht')) {
              $user->current_yacht = $request->current_yacht;
          }
          if ($request->has('current_yacht_start_date')) {
              try {
                  $user->current_yacht_start_date = \Carbon\Carbon::createFromFormat('m/d/Y', $request->current_yacht_start_date)->format('Y-m-d');
              } catch (\Exception $e) {
                  // If format is already Y-m-d, use as is
                  try {
                      $user->current_yacht_start_date = \Carbon\Carbon::parse($request->current_yacht_start_date)->format('Y-m-d');
                  } catch (\Exception $e2) {
                      // Invalid date, skip
                  }
              }
          }
          if ($request->has('sea_service_time_months')) {
              $user->sea_service_time_months = $request->sea_service_time_months;
          }
          if ($request->has('availability_status')) {
              $user->availability_status = $request->availability_status;
          }
          if ($request->has('availability_message')) {
              $user->availability_message = $request->availability_message;
          }
          if ($request->has('looking_to_meet')) {
              $user->looking_to_meet = filter_var($request->looking_to_meet, FILTER_VALIDATE_BOOLEAN);
          }
          if ($request->has('looking_for_work')) {
              $user->looking_for_work = filter_var($request->looking_for_work, FILTER_VALIDATE_BOOLEAN);
          }
          if ($request->has('languages')) {
              $user->languages = $request->languages;
          }
          if ($request->has('certifications')) {
              $user->certifications = $request->certifications;
          }
          if ($request->has('specializations')) {
              $user->specializations = $request->specializations;
          }
          if ($request->has('interests')) {
              $user->interests = $request->interests;
          }
          if ($request->has('previous_yachts')) {
              // Format previous yachts dates and validate
              $previousYachts = [];
              foreach ($request->previous_yachts as $yacht) {
                  $formattedYacht = [
                      'yacht_id' => $yacht['yacht_id'] ?? null,
                      'name' => $yacht['name'] ?? '',
                      'start_date' => null,
                      'end_date' => null,
                  ];
                  
                  if (!empty($yacht['start_date'])) {
                      try {
                          $formattedYacht['start_date'] = \Carbon\Carbon::createFromFormat('m/d/Y', $yacht['start_date'])->format('Y-m-d');
                      } catch (\Exception $e) {
                          try {
                              $formattedYacht['start_date'] = \Carbon\Carbon::parse($yacht['start_date'])->format('Y-m-d');
                          } catch (\Exception $e2) {
                              // Invalid date, skip
                          }
                      }
                  }
                  
                  if (!empty($yacht['end_date'])) {
                      try {
                          $formattedYacht['end_date'] = \Carbon\Carbon::createFromFormat('m/d/Y', $yacht['end_date'])->format('Y-m-d');
                      } catch (\Exception $e) {
                          try {
                              $formattedYacht['end_date'] = \Carbon\Carbon::parse($yacht['end_date'])->format('Y-m-d');
                          } catch (\Exception $e2) {
                              // Invalid date, skip
                          }
                      }
                  }
                  
                  // Validate end date is after start date
                  if ($formattedYacht['start_date'] && $formattedYacht['end_date']) {
                      $start = \Carbon\Carbon::parse($formattedYacht['start_date']);
                      $end = \Carbon\Carbon::parse($formattedYacht['end_date']);
                      if ($end->lt($start)) {
                          // Swap dates if end is before start
                          $temp = $formattedYacht['start_date'];
                          $formattedYacht['start_date'] = $formattedYacht['end_date'];
                          $formattedYacht['end_date'] = $temp;
                      }
                  }
                  
                  $previousYachts[] = $formattedYacht;
              }
              $user->previous_yachts = $previousYachts;
          }

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
              $user->birth_province
          ) {
              $user->status = true;
          }

          $user->save();

          return response()->json([
              'status'  => true,
              'message' => 'Profile updated successfully'
          ]);
      } catch (\Illuminate\Validation\ValidationException $e) {
          return response()->json([
              'status'  => false,
              'error'   => 'Validation failed',
              'details' => $e->errors()
          ], 422);
      } catch (\Exception $e) {
          return response()->json([
              'status'  => false,
              'error'   => 'Update failed',
              'details' => $e->getMessage()
          ], 500);
      }
  }
}
