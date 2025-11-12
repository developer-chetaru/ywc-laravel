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
use App\Http\Controllers\Itinerary\RouteController as SailingRouteController;
use App\Http\Controllers\Itinerary\CrewController as SailingCrewController;
use App\Http\Controllers\Itinerary\ReviewController as SailingReviewController;
use App\Http\Controllers\Itinerary\CommentController as SailingCommentController;

Route::apiResource('itineraries', ItineraryController::class);
Route::put('/itineraries/{itinerary}/status', [ItineraryController::class, 'updateStatus']);
Route::post('/itineraries/ai-generate', [ItineraryController::class, 'generateWithAI']);

Route::prefix('itinerary')->group(function () {
    // Route CRUD operations
    Route::get('/routes', [SailingRouteController::class, 'index']);
    Route::post('/routes', [SailingRouteController::class, 'store']);
    Route::get('/routes/{route}', [SailingRouteController::class, 'show']);
    Route::put('/routes/{route}', [SailingRouteController::class, 'update']);
    Route::delete('/routes/{route}', [SailingRouteController::class, 'destroy']);
    Route::post('/routes/{route}/clone', [SailingRouteController::class, 'cloneRoute']);
    Route::post('/routes/{route}/publish', [SailingRouteController::class, 'publish']);
    Route::get('/routes/{route}/statistics', [SailingRouteController::class, 'statistics']);
    Route::post('/routes/{route}/weather/refresh', [SailingRouteController::class, 'refreshWeather']);

    // Export routes (for mobile app - returns JSON with download URLs or file data)
    Route::get('/routes/{route}/export/pdf', [\App\Http\Controllers\Itinerary\ExportController::class, 'pdf']);
    Route::get('/routes/{route}/export/gpx', [\App\Http\Controllers\Itinerary\ExportController::class, 'gpx']);
    Route::get('/routes/{route}/export/xlsx', [\App\Http\Controllers\Itinerary\ExportController::class, 'xlsx']);

    // Crew management
    Route::get('/routes/{route}/crew', [SailingCrewController::class, 'index']);
    Route::post('/routes/{route}/crew', [SailingCrewController::class, 'store']);
    Route::put('/routes/{route}/crew/{crew}', [SailingCrewController::class, 'update']);
    Route::delete('/routes/{route}/crew/{crew}', [SailingCrewController::class, 'destroy']);
    Route::post('/routes/{route}/crew/{crew}/respond', [SailingCrewController::class, 'respond']);

    // Reviews
    Route::get('/routes/{route}/reviews', [SailingReviewController::class, 'index']);
    Route::post('/routes/{route}/reviews', [SailingReviewController::class, 'store']);
    Route::put('/routes/{route}/reviews/{review}', [SailingReviewController::class, 'update']);
    Route::delete('/routes/{route}/reviews/{review}', [SailingReviewController::class, 'destroy']);

    // Comments
    Route::get('/routes/{route}/comments', [SailingCommentController::class, 'index']);
    Route::post('/routes/{route}/comments', [SailingCommentController::class, 'store']);
    Route::put('/routes/{route}/comments/{comment}', [SailingCommentController::class, 'update']);
    Route::delete('/routes/{route}/comments/{comment}', [SailingCommentController::class, 'destroy']);
});

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






