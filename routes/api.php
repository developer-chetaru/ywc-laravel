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
use App\Http\Controllers\Api\YachtController;
use App\Http\Controllers\Api\YachtReviewController;
use App\Http\Controllers\Api\MarinaController;
use App\Http\Controllers\Api\MarinaReviewController;
use App\Http\Controllers\Api\ContractorController;
use App\Http\Controllers\Api\ContractorReviewController;
use App\Http\Controllers\Api\BrokerController;
use App\Http\Controllers\Api\BrokerReviewController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\RestaurantReviewController;
use App\Http\Controllers\Api\CrewDiscoveryController;
use App\Http\Controllers\Api\UserConnectionController;
use App\Http\Controllers\Api\RallyController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\CrewProfileController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\WorkLogController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\MentalHealthController;
use App\Http\Controllers\Api\TrainingController;

// Master Data API - Public endpoints for mobile developers
Route::get('/master-data', [MasterDataController::class, 'index']);
Route::get('/master-data/all', [MasterDataController::class, 'getAll']);
Route::get('/master-data/route-visibility', [MasterDataController::class, 'getRouteVisibility']);
Route::get('/master-data/route-status', [MasterDataController::class, 'getRouteStatus']);
Route::get('/master-data/marina-types', [MasterDataController::class, 'getMarinaTypes']);
Route::get('/master-data/yacht-types', [MasterDataController::class, 'getYachtTypes']);
Route::get('/master-data/countries', [MasterDataController::class, 'getCountries']);

Route::apiResource('itineraries', ItineraryController::class);
Route::put('/itineraries/{itinerary}/status', [ItineraryController::class, 'updateStatus']);
Route::post('/itineraries/ai-generate', [ItineraryController::class, 'generateWithAI']);

Route::prefix('itinerary')->group(function () {
    // Route CRUD operations (index and show are public, others require auth)
    Route::get('/routes/filter-options', [SailingRouteController::class, 'filterOptions']);
    Route::get('/routes', [SailingRouteController::class, 'index']);
    Route::get('/routes/{route}', [SailingRouteController::class, 'show']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/routes', [SailingRouteController::class, 'store']);
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
});

Route::get('/roles', [AuthController::class, 'getRoles']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/login', [AuthController::class, 'login']); // API login (returns Sanctum + legacy JWT token)
Route::post('/signup', [AuthController::class, 'signup']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Waitlist (public endpoint)
Route::post('/waitlist/join', [\App\Http\Controllers\LandingPageController::class, 'joinWaitlist']);

require base_path('vendor/riari/laravel-forum/routes/api.php');

Route::middleware('auth:sanctum')->group(function () {



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

    // Industry Review System - Authenticated Yacht Review Endpoints
    Route::post('/yachts/{yachtId}/reviews', [YachtReviewController::class, 'store']);
    Route::put('/yachts/{yachtId}/reviews/{reviewId}', [YachtReviewController::class, 'update']);
    Route::delete('/yachts/{yachtId}/reviews/{reviewId}', [YachtReviewController::class, 'destroy']);
    Route::post('/yachts/{yachtId}/reviews/{reviewId}/vote', [YachtReviewController::class, 'vote']);
    
    // Industry Review System - Authenticated Marina Review Endpoints
    Route::post('/marinas/{marinaId}/reviews', [MarinaReviewController::class, 'store']);
    Route::put('/marinas/{marinaId}/reviews/{reviewId}', [MarinaReviewController::class, 'update']);
    Route::delete('/marinas/{marinaId}/reviews/{reviewId}', [MarinaReviewController::class, 'destroy']);
    Route::post('/marinas/{marinaId}/reviews/{reviewId}/vote', [MarinaReviewController::class, 'vote']);

    // Industry Review System - Authenticated Contractor Review Endpoints
    Route::post('/contractors', [ContractorController::class, 'store']);
    Route::put('/contractors/{id}', [ContractorController::class, 'update']);
    Route::delete('/contractors/{id}', [ContractorController::class, 'destroy']);
    
    // Contractor Gallery
    Route::post('/contractors/{contractorId}/gallery', [ContractorController::class, 'addGalleryImage']);
    Route::delete('/contractors/{contractorId}/gallery/{imageId}', [ContractorController::class, 'deleteGalleryImage']);
    Route::put('/contractors/{contractorId}/gallery/order', [ContractorController::class, 'updateGalleryImageOrder']);

    Route::post('/contractors/{contractorId}/reviews', [ContractorReviewController::class, 'store']);
    Route::put('/contractors/{contractorId}/reviews/{reviewId}', [ContractorReviewController::class, 'update']);
    Route::delete('/contractors/{contractorId}/reviews/{reviewId}', [ContractorReviewController::class, 'destroy']);
    Route::post('/contractors/{contractorId}/reviews/{reviewId}/vote', [ContractorReviewController::class, 'vote']);

    // Industry Review System - Authenticated Broker Review Endpoints
    Route::post('/brokers', [BrokerController::class, 'store']);
    Route::put('/brokers/{id}', [BrokerController::class, 'update']);
    Route::delete('/brokers/{id}', [BrokerController::class, 'destroy']);
    
    // Broker Gallery
    Route::post('/brokers/{brokerId}/gallery', [BrokerController::class, 'addGalleryImage']);
    Route::delete('/brokers/{brokerId}/gallery/{imageId}', [BrokerController::class, 'deleteGalleryImage']);
    Route::put('/brokers/{brokerId}/gallery/order', [BrokerController::class, 'updateGalleryImageOrder']);

    Route::post('/brokers/{brokerId}/reviews', [BrokerReviewController::class, 'store']);
    Route::put('/brokers/{brokerId}/reviews/{reviewId}', [BrokerReviewController::class, 'update']);
    Route::delete('/brokers/{brokerId}/reviews/{reviewId}', [BrokerReviewController::class, 'destroy']);
    Route::post('/brokers/{brokerId}/reviews/{reviewId}/vote', [BrokerReviewController::class, 'vote']);

    // Industry Review System - Authenticated Restaurant Review Endpoints
    Route::post('/restaurants', [RestaurantController::class, 'store']);
    Route::put('/restaurants/{id}', [RestaurantController::class, 'update']);
    Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy']);
    
    // Restaurant Gallery
    Route::post('/restaurants/{restaurantId}/gallery', [RestaurantController::class, 'addGalleryImage']);
    Route::delete('/restaurants/{restaurantId}/gallery/{imageId}', [RestaurantController::class, 'deleteGalleryImage']);
    Route::put('/restaurants/{restaurantId}/gallery/order', [RestaurantController::class, 'updateGalleryImageOrder']);

    Route::post('/restaurants/{restaurantId}/reviews', [RestaurantReviewController::class, 'store']);
    Route::put('/restaurants/{restaurantId}/reviews/{reviewId}', [RestaurantReviewController::class, 'update']);
    Route::delete('/restaurants/{restaurantId}/reviews/{reviewId}', [RestaurantReviewController::class, 'destroy']);
    Route::post('/restaurants/{restaurantId}/reviews/{reviewId}/vote', [RestaurantReviewController::class, 'vote']);

    // Industry Review System - Yacht & Marina Management (Admin)
    Route::post('/yachts', [YachtController::class, 'store']);
    Route::put('/yachts/{id}', [YachtController::class, 'update']);
    Route::delete('/yachts/{id}', [YachtController::class, 'destroy']);
    
    // Yacht Gallery
    Route::post('/yachts/{yachtId}/gallery', [YachtController::class, 'addGalleryImage']);
    Route::delete('/yachts/{yachtId}/gallery/{imageId}', [YachtController::class, 'deleteGalleryImage']);
    Route::put('/yachts/{yachtId}/gallery/order', [YachtController::class, 'updateGalleryImageOrder']);
    
    Route::post('/marinas', [MarinaController::class, 'store']);
    Route::put('/marinas/{id}', [MarinaController::class, 'update']);
    Route::delete('/marinas/{id}', [MarinaController::class, 'destroy']);
    
    // Marina Gallery
    Route::post('/marinas/{marinaId}/gallery', [MarinaController::class, 'addGalleryImage']);
    Route::delete('/marinas/{marinaId}/gallery/{imageId}', [MarinaController::class, 'deleteGalleryImage']);
    Route::put('/marinas/{marinaId}/gallery/order', [MarinaController::class, 'updateGalleryImageOrder']);

    // Crew Profile & Privacy
    Route::prefix('crew-profile')->group(function () {
        Route::get('/', [CrewProfileController::class, 'getProfile']);
        Route::post('/update', [CrewProfileController::class, 'updateProfile']);
        Route::post('/privacy', [CrewProfileController::class, 'updatePrivacySettings']);
        Route::get('/{user}', [CrewProfileController::class, 'getPublicProfile']);
    });

            // Crew Discovery & Networking Features
            Route::prefix('crew-discovery')->group(function () {
                // Location management
                Route::post('/location/update', [CrewDiscoveryController::class, 'updateLocation']);
                Route::post('/online-status', [CrewDiscoveryController::class, 'updateOnlineStatus']);
                
                // Discovery
                Route::post('/discover', [CrewDiscoveryController::class, 'discoverNearby']);
                Route::get('/online', [CrewDiscoveryController::class, 'getOnlineCrew']);
                Route::get('/all-locations', [CrewDiscoveryController::class, 'getAllCrewLocations']);
            });

    // User Connections
    Route::prefix('connections')->group(function () {
        Route::post('/request', [UserConnectionController::class, 'sendRequest']);
        Route::get('/requests', [UserConnectionController::class, 'getRequests']);
        Route::post('/{connection}/accept', [UserConnectionController::class, 'acceptRequest']);
        Route::post('/{connection}/decline', [UserConnectionController::class, 'declineRequest']);
        Route::get('/list', [UserConnectionController::class, 'getConnections']);
        Route::delete('/{connection}', [UserConnectionController::class, 'removeConnection']);
        Route::post('/block', [UserConnectionController::class, 'blockUser']);
    });

    // Rallies (Crew Meetups/Events)
    Route::prefix('rallies')->group(function () {
        Route::post('/', [RallyController::class, 'store']);
        Route::get('/discover', [RallyController::class, 'discover']);
        Route::get('/{rally}', [RallyController::class, 'show']);
        Route::post('/{rally}/rsvp', [RallyController::class, 'rsvp']);
        Route::post('/{rally}/comments', [RallyController::class, 'addComment']);
    });

    // Messaging
    Route::prefix('messages')->group(function () {
        Route::post('/send', [MessageController::class, 'send']);
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        Route::get('/conversation/{user}', [MessageController::class, 'getConversation']);
        Route::post('/{user}/read', [MessageController::class, 'markAsRead']);
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount']);
    });

    // Work Log
    Route::prefix('work-logs')->group(function () {
        Route::get('/', [WorkLogController::class, 'index']);
        Route::post('/', [WorkLogController::class, 'store']);
        Route::get('/statistics', [WorkLogController::class, 'statistics']);
        Route::get('/compliance', [WorkLogController::class, 'compliance']);
        Route::get('/{id}', [WorkLogController::class, 'show']);
        Route::put('/{id}', [WorkLogController::class, 'update']);
        Route::delete('/{id}', [WorkLogController::class, 'destroy']);
        Route::post('/{id}/rest-periods', [WorkLogController::class, 'addRestPeriod']);
        Route::put('/{id}/rest-periods/{restPeriodId}', [WorkLogController::class, 'updateRestPeriod']);
        Route::delete('/{id}/rest-periods/{restPeriodId}', [WorkLogController::class, 'deleteRestPeriod']);
    });

    // Work Schedules
    Route::prefix('work-schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'index']);
        Route::get('/pending', [ScheduleController::class, 'pending']);
        Route::get('/templates', [ScheduleController::class, 'templates']);
        Route::get('/captain-summary', [ScheduleController::class, 'captainSummary']);
        Route::post('/', [ScheduleController::class, 'store']);
        Route::get('/{id}', [ScheduleController::class, 'show']);
        Route::put('/{id}', [ScheduleController::class, 'update']);
        Route::delete('/{id}', [ScheduleController::class, 'destroy']);
        Route::post('/{id}/confirm', [ScheduleController::class, 'confirm']);
        Route::post('/{id}/modify', [ScheduleController::class, 'modify']);
    });
});

// Optional public endpoints
Route::get('/career-history/public/certificate-issuers/{typeId}', [CareerHistoryApiController::class, 'getIssuersByType']);

// Industry Review System - Public Yacht & Marina Endpoints
Route::get('/yachts', [YachtController::class, 'index']);
Route::get('/yachts/{slug}', [YachtController::class, 'show']);
Route::get('/yachts/{yachtId}/reviews', [YachtReviewController::class, 'index']);
Route::get('/yachts/{yachtId}/reviews/{reviewId}', [YachtReviewController::class, 'show']);

Route::get('/marinas', [MarinaController::class, 'index']);
Route::get('/marinas/{slug}', [MarinaController::class, 'show']);
Route::get('/marinas/{marinaId}/reviews', [MarinaReviewController::class, 'index']);
Route::get('/marinas/{marinaId}/reviews/{reviewId}', [MarinaReviewController::class, 'show']);

// Industry Review System - Public Contractor Endpoints
Route::get('/contractors', [ContractorController::class, 'index']);
Route::get('/contractors/{slug}', [ContractorController::class, 'show']);
Route::get('/contractors/{contractorId}/reviews', [ContractorReviewController::class, 'index']);
Route::get('/contractors/{contractorId}/reviews/{reviewId}', [ContractorReviewController::class, 'show']);

// Industry Review System - Public Broker Endpoints
Route::get('/brokers', [BrokerController::class, 'index']);
Route::get('/brokers/{slug}', [BrokerController::class, 'show']);
Route::get('/brokers/{brokerId}/reviews', [BrokerReviewController::class, 'index']);
Route::get('/brokers/{brokerId}/reviews/{reviewId}', [BrokerReviewController::class, 'show']);

// Industry Review System - Public Restaurant Endpoints
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{slug}', [RestaurantController::class, 'show']);
Route::get('/restaurants/{restaurantId}/reviews', [RestaurantReviewController::class, 'index']);
Route::get('/restaurants/{restaurantId}/reviews/{reviewId}', [RestaurantReviewController::class, 'show']);

// Mental Health & Wellness Support - Public Endpoints
Route::prefix('mental-health')->group(function () {
    Route::get('/therapists', [MentalHealthController::class, 'getTherapists']);
    // More specific route must come before less specific route
    Route::get('/therapists/{id}/availability', [MentalHealthController::class, 'getTherapistAvailability'])->middleware('auth:sanctum');
    Route::get('/therapists/{id}', [MentalHealthController::class, 'getTherapist']);
    Route::get('/resources', [MentalHealthController::class, 'getResources']);
    Route::get('/filter-options', [MentalHealthController::class, 'getFilterOptions']);
    
    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', [MentalHealthController::class, 'getDashboard']);
    });
});

// Training & Resources - Public Endpoints
Route::prefix('training')->group(function () {
    Route::get('/providers', [TrainingController::class, 'getProviders']);
    Route::get('/providers/{slug}', [TrainingController::class, 'getProvider']);
    Route::get('/courses', [TrainingController::class, 'getCourses']);
    Route::get('/courses/{id}', [TrainingController::class, 'getCourse']);
    Route::get('/certifications', [TrainingController::class, 'getCertifications']);
    Route::get('/categories', [TrainingController::class, 'getCategories']);
    Route::get('/schedules', [TrainingController::class, 'getSchedules']);
    Route::get('/filter-options', [TrainingController::class, 'getFilterOptions']);
});

// Optional token refresh route
Route::middleware('jwt.refresh')->get('/token/refresh', function () {
    return response()->json(['status' => 'token refreshed']);
});

// Route::post('/forgot-password', [ForgetPasswordController::class, 'sendOtp']);
// Route::post('/reset-password', [ForgetPasswordController::class, 'resetPassword']);

Route::post('auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);

Route::get('/verify-user/{id}', [VerificationController::class, 'verify'])
    ->name('api.user.verify')
    ->middleware('signed'); 






