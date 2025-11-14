<?php

use App\Livewire\ManageDocument;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ManageDocumentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\CareerHistory;
use App\Livewire\Profile;
use App\Livewire\UpdatePasswordForm;
use App\Livewire\PurchaseHistory;
use App\Livewire\UserList;
use App\Livewire\ItinerarySystem;
use App\Livewire\Itinerary\RoutePlanner;
use App\Livewire\Itinerary\RouteLibrary;
use App\Models\ItineraryRoute;
use App\Livewire\LegalSupport;
use App\Livewire\Certificate\CertificateTypeIndex;
use App\Http\Controllers\CertificateTypeController;
use App\Livewire\Certificate\CertificateIssuerIndex;

use App\Livewire\Marketplace\ItineraryIndex;
use App\Livewire\Marketplace\MarketplaceIndex;
use App\Livewire\WorkLog\WorkLogIndex;
use App\Livewire\IndustryReview\IndustryReviewIndex;

use App\Livewire\Roles\RoleIndex;
use App\Livewire\CrewDiscovery;
use App\Livewire\UserConnections;
use App\Livewire\RallyManager;

use App\Http\Controllers\CareerHistoryController;
use App\Http\Controllers\CocCheckerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ProfileController;
use App\Livewire\TrainingResources;

use App\Models\Order as InternalOrder;
use App\Livewire\SubscriptionPage;
use App\Http\Controllers\SubscriptionController;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;



Route::get('/', function () {
    return redirect()->to('https://console-ywc.nativeappdev.com/login');
});

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');


Route::get('/test-reset-mail', function () {
    $user = \App\Models\User::find(32);
    $token = app('auth.password.broker')->createToken($user);
    $user->sendPasswordResetNotification($token);
    return 'Custom reset password mail sent!';
});




// 🚪 Subscription page (accessible without active subscription but still requires login)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified','setlocale'])
    ->group(function () {
        Route::get('/subscription', SubscriptionPage::class)->name('subscription.page');

    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');

    Route::get('/subscription/cancel', function () {
        return redirect()->route('subscription.page')->with('failed', 'Payment cancelled or failed!');
    })->name('subscription.cancel');
    });


// Example profile route
    Route::get('/profile/{encryptedId}', [ProfileController::class, 'show'])->name('profile.show.public');
	Route::get('/p/{encryptedId}', [ProfileController::class, 'showPublic'])->name('profile.public');



// 🔒 Routes that require subscription
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'subscribed',
    'setlocale',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    
    
    
    Route::get('/manage-document', ManageDocument::class)->name('manage-document');
  
  	Route::prefix('forum')->name('forum.')->group(function () {
        require base_path('vendor/riari/laravel-forum/routes/livewire.php');
    });
  
    Route::get('/forums', [ManageDocumentController::class, 'forums'])->name('forum');
    Route::get('/documents', [ManageDocumentController::class, 'documents'])->name('documents');
    Route::get('/mental-health', [ManageDocumentController::class, 'mentalHealth'])->name('mental-health');
    Route::get('/training', [ManageDocumentController::class, 'training'])->name('training');
    Route::get('/weather', [ManageDocumentController::class, 'weather'])->name('weather');
    Route::get('/review', [ManageDocumentController::class, 'review'])->name('review');
    Route::get('/itinerary-system', [ManageDocumentController::class, 'itinerarySystem'])->name('itinerary-system');
  
    Route::get('/certificate-types', [CertificateTypeController::class, 'index'])->name('certificate-types.index');
    Route::patch('/certificate-type/{id}/toggle', [CertificateTypeController::class, 'toggleActive'])->name('certificate-type.toggle');
  
    //Route::get('/career-history', CareerHistory::class)->name('career-history');
  
  	Route::get('/career-history', [CareerHistoryController::class, 'index'])->name('career-history');
    Route::get('/certificate-type/{id}/issuers', [CareerHistoryController::class, 'getIssuersByType']);
    Route::post('/documents/scan', [CareerHistoryController::class, 'scan'])->name('documents.scan');
    Route::post('/career-history', [CareerHistoryController::class, 'store'])->name('career-history.store');
  	Route::post('/documents/toggle-share', [CareerHistoryController::class, 'toggleShare'])->name('documents.toggleShare');
	
  	Route::get('/career-history/{id}', [CareerHistoryController::class, 'show'])->name('career-history.show');
  	Route::patch('/admin/documents/{document}/status', [CareerHistoryController::class, 'updateStatus']);
    // Route::post('/career-history/docs/{doc}/toggle', [CareerHistoryController::class, 'toggleDoc'])->name('career-history.docs.toggle');

  	Route::get('/career-history/documents/{id}/edit', [CareerHistoryController::class, 'getDocumentForEdit'])->name('career-history.documents.edit');
    Route::put('/career-history/{id}', [CareerHistoryController::class, 'update'])->name('career-history.update');

  	Route::post('/admin/documents/{document}/verify', [CocCheckerController::class, 'verify']);
    Route::patch('/admin/documents/{document}/status', [CocCheckerController::class, 'updateStatus']);  	
  
    Route::post('/documents/share', [DocumentController::class, 'share'])->name('documents.share');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('/profile/share', [ProfileController::class, 'share'])->name('profile.share');

  
  	Route::get('/qrcode', [QRCodeController::class, 'generate'])->name('qrcode.generate');
  
  	Route::get('/profile', Profile::class)->name('profile');
    Route::get('/change-password', UpdatePasswordForm::class)->name('profile.password');
    

	
	
  
  	Route::get('/purchase-history', PurchaseHistory::class)->name('purchase.history');
	Route::get('/users', UserList::class)->name('users.index');
  	Route::get('/itinerary-system', ItinerarySystem::class)->name('itinerary.system');
  	// Route::get('/certificate-types', CertificateTypeIndex::class)->name('certificate-types.index');
	
    Route::get('/career-history/issuers', [CareerHistoryController::class, 'getIssuers'])->name('career-history.issuers');

    Route::get('/certificate-types/create', [CertificateTypeController::class, 'create'])->name('certificate-type.create');
    Route::post('/certificate-types', [CertificateTypeController::class, 'store'])->name('certificate-type.store');
    Route::get('certificate-types/{id}/edit', [CertificateTypeController::class, 'edit'])->name('certificate-type.edit');
    Route::put('certificate-types/{id}', [CertificateTypeController::class, 'update'])->name('certificate-type.update');
  	Route::delete('/certificate-type/{id}', [CertificateTypeController::class, 'destroy'])->name('certificate-type.destroy');

    Route::get('/certificate-issuers', CertificateIssuerIndex::class)->name('certificate.issuers.index');
  
  	Route::get('/legal-support', LegalSupport::class)->name('legal-support.index');
  
  	Route::get('/training-resources', TrainingResources::class)->name('training.resources');

	Route::get('/roles', RoleIndex::class)->name('roles.index');

	// Crew Discovery & Networking (API-based, accessible from sidebar)
	Route::get('/crew-discovery', CrewDiscovery::class)->name('crew.discovery');
	Route::get('/connections', UserConnections::class)->name('user.connections');
	Route::get('/rallies', RallyManager::class)->name('rallies.index');

  Route::get('/itinerary', ItineraryIndex::class)->name('itinerary.index');
	Route::get('/marketplace', MarketplaceIndex::class)->name('marketplace.index');
	
	// Itinerary Routes - Web views (Livewire components)
	Route::get('/itinerary/routes', \App\Livewire\Itinerary\RouteLibrary::class)->name('itinerary.routes.index');
	Route::get('/itinerary/routes/planner', \App\Livewire\Itinerary\RoutePlanner::class)->name('itinerary.routes.planner');
	
	// Route show - works for both web (view) and API (JSON) requests
	Route::get('/itinerary/routes/{route}', function (\Illuminate\Http\Request $request, \App\Models\ItineraryRoute $route) {
	    // Check authorization
	    \Illuminate\Support\Facades\Gate::authorize('view', $route);
	    
	    // Load route data with all relationships (same as API endpoint)
	    $route->loadMissing([
	        'stops.weatherSnapshots',
	        'legs.from',
	        'legs.to',
	        'crew.user:id,first_name,last_name,email',
	        'reviews.user:id,first_name,last_name',
	        'statistics',
	        'owner:id,first_name,last_name,email',
	    ]);
	    
	    $storage = \Illuminate\Support\Facades\Storage::disk('public');
	    
	    // Ensure photos are properly formatted for each stop
	    foreach ($route->stops as $stop) {
	        // If photos is a string, decode it
	        if (is_string($stop->photos)) {
	            $decoded = json_decode($stop->photos, true);
	            $stop->photos = is_array($decoded) ? $decoded : [];
	        }
	        // If photos is null, set to empty array
	        if ($stop->photos === null) {
	            $stop->photos = [];
	        }
	        // Ensure it's an array
	        if (!is_array($stop->photos)) {
	            $stop->photos = [];
	        }
	        
	        // For API requests, convert to URL format; for web, keep as paths
	        if ($request->wantsJson() || $request->is('api/*')) {
	            // API format: return with URLs
	            $stop->photos = array_values(array_filter(
	                array_map(function($photo) use ($storage) {
	                    if (empty($photo) || !is_string($photo)) {
	                        return null;
	                    }
	                    if ($storage->exists($photo)) {
	                        return [
	                            'path' => $photo,
	                            'url' => asset('storage/' . $photo),
	                        ];
	                    }
	                    return null;
	                }, $stop->photos),
	                fn($photo) => $photo !== null
	            ));
	        } else {
	            // Web format: keep as simple paths
	            $stop->photos = array_values(array_filter($stop->photos, function($photo) use ($storage) {
	                return !empty($photo) && is_string($photo) && $storage->exists($photo);
	            }));
	        }
	        
	        // Set the photos attribute directly to ensure it's used
	        $stop->setAttribute('photos', $stop->photos);
	    }
	    
	    // Convert cover_image to full URL for API requests
	    if ($request->wantsJson() || $request->is('api/*')) {
	        if ($route->cover_image) {
	            $route->cover_image_url = asset('storage/' . $route->cover_image);
	        }
	        
	        return response()->json([
	            'data' => $route,
	        ]);
	    }
	    
	    // Return view for web requests
	    return view('itinerary.route-show', ['route' => $route]);
	})->name('itinerary.routes.show');
    Route::get('/work-log', WorkLogIndex::class)->name('worklog.index');
    Route::get('/industry-review', IndustryReviewIndex::class)->name('industryreview.index');

    // Industry Review Management (Admin only - uses API endpoints)
    Route::get('/industry-review/yachts', \App\Livewire\IndustryReview\YachtManage::class)->name('industryreview.yachts.manage');
    Route::get('/industry-review/marinas', \App\Livewire\IndustryReview\MarinaManage::class)->name('industryreview.marinas.manage');

});


Route::get('/verify-user/{id}', [VerificationController::class, 'verify'])
    ->name('user.verify')
    ->middleware('signed'); 
    