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
use App\Livewire\LegalSupport;
use App\Livewire\Certificate\CertificateTypeIndex;
use App\Http\Controllers\CertificateTypeController;
use App\Livewire\Certificate\CertificateIssuerIndex;

use App\Livewire\Marketplace\MarketplaceIndex;
use App\Livewire\WorkLog\WorkLogIndex;
use App\Livewire\IndustryReview\IndustryReviewIndex;

use App\Livewire\Roles\RoleIndex;

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
	Route::get('/marketplace', MarketplaceIndex::class)->name('marketplace.index');
    Route::get('/work-log', WorkLogIndex::class)->name('worklog.index');
    Route::get('/industry-review', IndustryReviewIndex::class)->name('industryreview.index');

});


Route::get('/verify-user/{id}', [VerificationController::class, 'verify'])
    ->name('user.verify')
    ->middleware('signed'); 
    