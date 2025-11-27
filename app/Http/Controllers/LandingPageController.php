<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function joinWaitlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:waitlists,email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $waitlist = Waitlist::create([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'role' => $request->role,
                'status' => 'pending',
                'source' => $request->get('source', 'landing_page'),
            ]);

            // Track conversion in analytics if available
            if (function_exists('gtag')) {
                // This will be handled by frontend JavaScript
            }

            // TODO: Send confirmation email to user
            // TODO: Send notification to admin about new waitlist signup

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! You\'ve been added to our waitlist. We\'ll notify you when the platform is ready.',
                    'data' => $waitlist
                ], 200);
            }

            return back()->with('success', 'Thank you! You\'ve been added to our waitlist. We\'ll notify you when the platform is ready.');
        } catch (\Exception $e) {
            \Log::error('Waitlist signup error: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.'
                ], 500);
            }
            
            return back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
