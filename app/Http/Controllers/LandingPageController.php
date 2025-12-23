<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function joinWaitlist(Request $request)
    {
        \Log::info('Waitlist signup attempt', [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'source' => $request->get('source', 'landing_page'),
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:waitlists,email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            \Log::warning('Waitlist signup validation failed', [
                'email' => $request->email,
                'errors' => $validator->errors()->toArray(),
            ]);

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

            \Log::info('Waitlist entry created successfully', [
                'waitlist_id' => $waitlist->id,
                'email' => $waitlist->email,
            ]);

            // Track conversion in analytics if available
            if (function_exists('gtag')) {
                // This will be handled by frontend JavaScript
            }

            // Send confirmation via OneSignal (email channel using external_user_id = email)
            try {
                $appId = config('services.onesignal.app_id');
                $apiKey = config('services.onesignal.rest_api_key');

                \Log::info('Attempting to send waitlist confirmation via OneSignal', [
                    'waitlist_id' => $waitlist->id,
                    'email' => $waitlist->email,
                    'first_name' => $waitlist->first_name,
                    'last_name' => $waitlist->last_name,
                    'role' => $waitlist->role,
                    'onesignal_app_id' => $appId ? 'set' : 'missing',
                ]);

                if ($appId && $apiKey) {
                    $emailBody = view('emails.waitlist-confirmation', [
                        'waitlist' => $waitlist,
                    ])->render();

                    $response = Http::withHeaders([
                        'Authorization' => 'Basic ' . $apiKey,
                        'Content-Type'  => 'application/json; charset=utf-8',
                    ])->post('https://api.onesignal.com/notifications', [
                        'app_id' => $appId,
                        'include_external_user_ids' => [$waitlist->email],
                        'channel_for_external_user_ids' => 'email',
                        'email_subject' => 'Welcome to Yacht Workers Council Waitlist',
                        'email_body' => $emailBody,
                    ]);

                    \Log::info('OneSignal waitlist notification response', [
                        'status' => $response->status(),
                        'body' => $response->json(),
                    ]);
                } else {
                    \Log::warning('OneSignal credentials missing, cannot send waitlist confirmation', [
                        'app_id_present' => (bool) $appId,
                        'api_key_present' => (bool) $apiKey,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send waitlist confirmation via OneSignal', [
                    'waitlist_id' => $waitlist->id,
                    'email' => $waitlist->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't fail the request if notification fails
            }

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
