<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CareerHistoryApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ItineraryController;

Route::apiResource('itineraries', ItineraryController::class);
Route::put('/itineraries/{itinerary}/status', [ItineraryController::class, 'updateStatus']);


Route::get('/roles', [AuthController::class, 'getRoles']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/login', [AuthController::class, 'login']);
Route::get('/roles', [AuthController::class, 'getRoles']);
Route::post('/signup', [AuthController::class, 'signup']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

require base_path('vendor/riari/laravel-forum/routes/api.php');

Route::middleware('auth:api')->group(function () {



	Route::get('/profile', [ProfileController::class, 'profile']);
    Route::post('/profile', [ProfileController::class, 'updateProfile']); 
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);

    Route::post('/career-history/upload', [CareerHistoryApiController::class, 'uploadDocument']);
    
    Route::get('/career-history/documents', [CareerHistoryApiController::class, 'list']);
    Route::get('/career-history/documents/{id}', [CareerHistoryApiController::class, 'showDocument']);
    Route::post('/career-history/documents/share', [CareerHistoryApiController::class, 'shareDocument']);
    Route::post('/career-history/documents/{id}/toggle-share', [CareerHistoryApiController::class, 'toggleShare']);
    Route::post('/career-history/documents/{id}/toggle-preview', [CareerHistoryApiController::class, 'togglePreview']);
    Route::get('/career-history/share-profile', [CareerHistoryApiController::class, 'shareProfile']);
    Route::post('/career-history/scan', [CareerHistoryApiController::class, 'scan']);
    Route::get('/career-history/issue-countries', [CareerHistoryApiController::class, 'issueCountries']);
    Route::get('/career-history/certificate-types', [CareerHistoryApiController::class, 'certificateTypes']);
});

// Optional public endpoints
Route::get('/career-history/public/certificate-issuers/{typeId}', [CareerHistoryApiController::class, 'getIssuersByType']);

// Optional token refresh route
Route::middleware('jwt.refresh')->get('/token/refresh', function () {
    return response()->json(['status' => 'token refreshed']);
});

// Route::post('/forgot-password', [ForgetPasswordController::class, 'sendOtp']);
// Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword']);

Route::post('auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);

Route::get('/verify-user/{id}', [VerificationController::class, 'verify'])
    ->name('user.verify')
    ->middleware('signed'); 






