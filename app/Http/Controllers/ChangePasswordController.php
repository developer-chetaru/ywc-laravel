<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    /**
     * Change user password via API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            'password.confirmed' => 'The password confirmation does not match.',
            'current_password.required' => 'The current password field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The current password is incorrect.'
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ], 200);
    }
}

