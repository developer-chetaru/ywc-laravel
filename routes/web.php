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
use App\Livewire\WorkLog\ScheduleManager;
use App\Livewire\WorkLog\CaptainDashboard;
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
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\WaitlistAdminController;
use App\Livewire\TrainingResources;
use App\Livewire\Training\CourseDiscovery;
use App\Livewire\Training\CertificationDetail;
use App\Livewire\Training\UserCertificationDashboard;
use App\Livewire\Training\ProviderPortal;
use App\Livewire\Training\Admin\TrainingAdminDashboard;
use App\Livewire\Training\Admin\ManageCertifications;
use App\Livewire\Training\Admin\CertificationForm;
use App\Livewire\Training\Admin\ManageProviders;
use App\Livewire\Training\Admin\ProviderForm;
use App\Livewire\Training\Admin\ManageCourses;
use App\Livewire\Training\Admin\ManageReviews;
use App\Livewire\Training\Admin\ManageCategories;

use App\Models\Order as InternalOrder;
use App\Livewire\SubscriptionPage;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;



Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::post('/waitlist/join', [LandingPageController::class, 'joinWaitlist'])->name('waitlist.join');

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook');

// Financial Future Planning - Public route (accessible to all)
Route::get('/financial-future-planning', function () {
    // Redirect logged-in users to dashboard, guests to calculators
    if (Auth::check()) {
        return redirect()->route('financial.dashboard');
    }
    return redirect()->route('financial.calculators.index');
})->name('financial-future-planning');

// Financial Planning Calculators (Public - No login required)
use App\Livewire\FinancialPlanning\CalculatorsIndex;
use App\Livewire\FinancialPlanning\RetirementNeedsCalculator;
use App\Livewire\FinancialPlanning\CompoundInterestCalculator;
use App\Livewire\FinancialPlanning\PensionGrowthCalculator;
use App\Livewire\FinancialPlanning\FourPercentRuleCalculator;
use App\Livewire\FinancialPlanning\CostOfWaitingCalculator;
use App\Livewire\FinancialPlanning\FireCalculator;
use App\Livewire\FinancialPlanning\InvestmentReturnProjector;
use App\Livewire\FinancialPlanning\AssetAllocationAnalyzer;
use App\Livewire\FinancialPlanning\PortfolioRiskCalculator;
use App\Livewire\FinancialPlanning\DCASimulator;
use App\Livewire\FinancialPlanning\DividendIncomeCalculator;
use App\Livewire\FinancialPlanning\YachtCrewBudgetCalculator;
use App\Livewire\FinancialPlanning\EmergencyFundCalculator;
use App\Livewire\FinancialPlanning\SavingsRateCalculator;
use App\Livewire\FinancialPlanning\TimeOffExpensePlanner;
use App\Livewire\FinancialPlanning\DebtPayoffCalculator;
use App\Livewire\FinancialPlanning\MortgageAffordabilityCalculator;
use App\Livewire\FinancialPlanning\BuyVsRentCalculator;
use App\Livewire\FinancialPlanning\RentalPropertyAnalyzer;
use App\Livewire\FinancialPlanning\PropertyAppreciationCalculator;
use App\Livewire\FinancialPlanning\RealEstateROICalculator;
use App\Livewire\FinancialPlanning\IncomeTaxEstimator;
use App\Livewire\FinancialPlanning\TaxEfficientWithdrawalCalculator;
use App\Livewire\FinancialPlanning\CapitalGainsTaxCalculator;
use App\Livewire\FinancialPlanning\FinancialDashboard;

// Financial Planning Calculators - PUBLIC ACCESS (no auth required)
// These must be registered BEFORE the auth middleware group to allow guest access
Route::prefix('financial-planning')->name('financial.')
    ->group(function () {
    
    // Calculators index - accessible to both guests and logged-in users  
    Route::get('/calculators', CalculatorsIndex::class)->name('calculators.index');
    
    // Retirement & Pension
    Route::get('/calculators/retirement-needs', RetirementNeedsCalculator::class)->name('calculators.retirement-needs');
    Route::get('/calculators/pension-growth', PensionGrowthCalculator::class)->name('calculators.pension-growth');
    Route::get('/calculators/four-percent-rule', FourPercentRuleCalculator::class)->name('calculators.four-percent-rule');
    Route::get('/calculators/cost-of-waiting', CostOfWaitingCalculator::class)->name('calculators.cost-of-waiting');
    Route::get('/calculators/fire', FireCalculator::class)->name('calculators.fire');
    
    // Investment
    Route::get('/calculators/compound-interest', CompoundInterestCalculator::class)->name('calculators.compound-interest');
    Route::get('/calculators/investment-return', InvestmentReturnProjector::class)->name('calculators.investment-return');
    Route::get('/calculators/asset-allocation', AssetAllocationAnalyzer::class)->name('calculators.asset-allocation');
    Route::get('/calculators/portfolio-risk', PortfolioRiskCalculator::class)->name('calculators.portfolio-risk');
    Route::get('/calculators/dca-simulator', DCASimulator::class)->name('calculators.dca-simulator');
    Route::get('/calculators/dividend-income', DividendIncomeCalculator::class)->name('calculators.dividend-income');
    
    // Savings & Budget
    Route::get('/calculators/yacht-crew-budget', YachtCrewBudgetCalculator::class)->name('calculators.yacht-crew-budget');
    Route::get('/calculators/emergency-fund', EmergencyFundCalculator::class)->name('calculators.emergency-fund');
    Route::get('/calculators/savings-rate', SavingsRateCalculator::class)->name('calculators.savings-rate');
    Route::get('/calculators/time-off-expense', TimeOffExpensePlanner::class)->name('calculators.time-off-expense');
    
    // Debt & Loans
    Route::get('/calculators/debt-payoff', DebtPayoffCalculator::class)->name('calculators.debt-payoff');
    Route::get('/calculators/mortgage-affordability', MortgageAffordabilityCalculator::class)->name('calculators.mortgage-affordability');
    Route::get('/calculators/buy-vs-rent', BuyVsRentCalculator::class)->name('calculators.buy-vs-rent');
    
    // Property Investment
    Route::get('/calculators/rental-property', RentalPropertyAnalyzer::class)->name('calculators.rental-property');
    Route::get('/calculators/property-appreciation', PropertyAppreciationCalculator::class)->name('calculators.property-appreciation');
    Route::get('/calculators/real-estate-roi', RealEstateROICalculator::class)->name('calculators.real-estate-roi');
    
    // Tax Calculators
    Route::get('/calculators/income-tax', IncomeTaxEstimator::class)->name('calculators.income-tax');
    Route::get('/calculators/tax-efficient-withdrawal', TaxEfficientWithdrawalCalculator::class)->name('calculators.tax-efficient-withdrawal');
    Route::get('/calculators/capital-gains-tax', CapitalGainsTaxCalculator::class)->name('calculators.capital-gains-tax');
});

// Financial Planning routes - REQUIRES AUTHENTICATION
// MUST be registered BEFORE the subscribed middleware group
// to ensure they're matched correctly and bypass subscription checks
// These routes ONLY require 'auth' middleware - NO 'subscribed' middleware
// Use 'auth:web' explicitly to match Fortify's authentication guard
Route::prefix('financial-planning')->name('financial.')
    ->middleware('auth:web')
    ->group(function () {
    
    Route::get('/dashboard', FinancialDashboard::class)->name('dashboard'); // Route name will be 'financial.dashboard' due to prefix
    
    // Management pages (requires auth)
    Route::get('/accounts', \App\Livewire\FinancialPlanning\AccountManagement::class)->middleware('auth')->name('accounts.index');
    Route::get('/goals', \App\Livewire\FinancialPlanning\GoalManagement::class)->middleware('auth')->name('goals.index');
    Route::get('/transactions', \App\Livewire\FinancialPlanning\TransactionManagement::class)->middleware('auth')->name('transactions.index');
    Route::get('/budget', \App\Livewire\FinancialPlanning\BudgetManagement::class)->middleware('auth')->name('budget.index');
    Route::get('/reports', \App\Livewire\FinancialPlanning\FinancialReports::class)->middleware('auth')->name('reports.index');
    Route::get('/retirement-planner', \App\Livewire\FinancialPlanning\RetirementPlanner::class)->middleware('auth')->name('retirement-planner');
    Route::get('/education', \App\Livewire\FinancialPlanning\EducationalContent::class)->middleware('auth')->name('education.index');
    Route::get('/tax-planner', \App\Livewire\FinancialPlanning\TaxPlanner::class)->middleware('auth')->name('tax-planner');
    Route::get('/advisory', \App\Livewire\FinancialPlanning\AdvisoryServices::class)->middleware('auth')->name('advisory.index');
    Route::get('/success-stories', \App\Livewire\FinancialPlanning\SuccessStories::class)->name('success-stories.index');
    Route::get('/notifications', \App\Livewire\FinancialPlanning\FinancialNotifications::class)->middleware('auth')->name('notifications.index');
    
    // Admin routes - Using custom middleware to check role with api guard
    Route::get('/admin', \App\Livewire\FinancialPlanning\Admin\FinancialAdminPanel::class)
        ->middleware(['auth', 'financial.admin'])
        ->name('admin.index');
    
    // Note: Calculator routes are in the PUBLIC group above (lines 101-143) - accessible to guests
});

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// Document Approval - PUBLIC ROUTE (no auth required, accessible via token or login form)
// Must be before /documents/{id} route to avoid conflict
Route::get('/documents/approval', function() {
    return view('documents.approval');
})->name('documents.approval');

// Employer Dashboard Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale', 'role:employer'])
    ->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\EmployerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/add-crew', function() { return view('employer.add-crew'); })->name('add-crew-page');
        Route::get('/crew/{id}', [\App\Http\Controllers\EmployerDashboardController::class, 'showCrewMember'])->name('crew-details');
        Route::get('/crew/{id}/edit', [\App\Http\Controllers\EmployerDashboardController::class, 'editCrewPage'])->name('edit-crew-page');
        Route::post('/crew/add', [\App\Http\Controllers\EmployerDashboardController::class, 'addCrew'])->name('add-crew');
        Route::post('/crew/{id}/update', [\App\Http\Controllers\EmployerDashboardController::class, 'updateCrew'])->name('update-crew');
        Route::delete('/crew/{id}/remove', [\App\Http\Controllers\EmployerDashboardController::class, 'removeCrew'])->name('remove-crew');
        Route::get('/compliance-report', [\App\Http\Controllers\EmployerDashboardController::class, 'complianceReport'])->name('compliance-report');
        Route::get('/export-compliance', [\App\Http\Controllers\EmployerDashboardController::class, 'exportComplianceReport'])->name('export-compliance');
    });

// Recruitment Agency Dashboard Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale', 'role:recruitment_agency'])
    ->prefix('agency')->name('agency.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\RecruitmentAgencyController::class, 'index'])->name('dashboard');
        Route::get('/candidates/{id}', [\App\Http\Controllers\RecruitmentAgencyController::class, 'showCandidate'])->name('candidate-details');
        Route::post('/candidates/add', [\App\Http\Controllers\RecruitmentAgencyController::class, 'addCandidate'])->name('add-candidate');
        Route::post('/candidates/{id}/update', [\App\Http\Controllers\RecruitmentAgencyController::class, 'updateCandidate'])->name('update-candidate');
        Route::delete('/candidates/{id}/remove', [\App\Http\Controllers\RecruitmentAgencyController::class, 'removeCandidate'])->name('remove-candidate');
        Route::get('/jobs', [\App\Http\Controllers\RecruitmentAgencyController::class, 'jobs'])->name('jobs');
        Route::post('/jobs/create', [\App\Http\Controllers\RecruitmentAgencyController::class, 'createJob'])->name('create-job');
        Route::post('/jobs/{id}/update', [\App\Http\Controllers\RecruitmentAgencyController::class, 'updateJob'])->name('update-job');
        Route::delete('/jobs/{id}/delete', [\App\Http\Controllers\RecruitmentAgencyController::class, 'deleteJob'])->name('delete-job');
        Route::get('/jobs/{id}/matches', [\App\Http\Controllers\RecruitmentAgencyController::class, 'matchCandidates'])->name('job-matches');
    });

// Training Provider Dashboard Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale'])
    ->prefix('training-provider')->name('training-provider.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TrainingProviderController::class, 'index'])->name('dashboard');
        Route::get('/issue-certificate', [\App\Http\Controllers\TrainingProviderController::class, 'showIssueForm'])->name('issue-form');
        Route::post('/issue-certificate', [\App\Http\Controllers\TrainingProviderController::class, 'issueCertificate'])->name('issue');
        Route::get('/certificates/{id}', [\App\Http\Controllers\TrainingProviderController::class, 'viewCertificate'])->name('view-certificate');
        Route::post('/certificates/{id}/revoke', [\App\Http\Controllers\TrainingProviderController::class, 'revokeCertificate'])->name('revoke-certificate');
        Route::post('/certificates/{id}/reactivate', [\App\Http\Controllers\TrainingProviderController::class, 'reactivateCertificate'])->name('reactivate-certificate');
        Route::get('/certificates/{id}/download', [\App\Http\Controllers\TrainingProviderController::class, 'downloadCertificate'])->name('download-certificate');
        Route::post('/bulk-issue', [\App\Http\Controllers\TrainingProviderController::class, 'bulkIssueCertificates'])->name('bulk-issue');
    });

// Analytics Routes - Requires Subscription
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale', 'subscribed'])
    ->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/user-dashboard', [\App\Http\Controllers\AnalyticsController::class, 'userDashboard'])->name('user-dashboard');
        Route::get('/employer-dashboard', [\App\Http\Controllers\AnalyticsController::class, 'employerDashboard'])->name('employer-dashboard');
        Route::get('/report-builder', [\App\Http\Controllers\AnalyticsController::class, 'reportBuilder'])->name('report-builder');
        Route::post('/export-report', [\App\Http\Controllers\AnalyticsController::class, 'exportReport'])->name('export-report');
    });

// Share Wizard & Templates Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale'])->group(function () {
    Route::get('/documents/{document}/share-wizard', [\App\Http\Controllers\ShareWizardController::class, 'showWizard'])->name('share-wizard.show');
    Route::post('/documents/{document}/share-wizard', [\App\Http\Controllers\ShareWizardController::class, 'createShare'])->name('share-wizard.create');
    
    Route::prefix('share-templates')->name('share-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ShareTemplateController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ShareTemplateController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ShareTemplateController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\ShareTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\ShareTemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\ShareTemplateController::class, 'destroy'])->name('destroy');
    });
});

// Public Share Routes (no auth required)
Route::prefix('shared')->name('shared.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\ShareWizardController::class, 'viewShared'])->name('view');
    Route::post('/{token}/verify-password', [\App\Http\Controllers\ShareWizardController::class, 'verifyPassword'])->name('verify-password');
    Route::get('/{token}/download', [\App\Http\Controllers\ShareWizardController::class, 'downloadShared'])->name('download');
});

// Public Document Share Routes (no auth required) - Must be outside auth middleware
Route::get('/documents/share/{token}', [\App\Http\Controllers\DocumentShareController::class, 'view'])->name('documents.share.view');
Route::post('/documents/share/{token}/verify-password', [\App\Http\Controllers\DocumentShareController::class, 'verifyPassword'])->name('documents.share.verify-password');
Route::get('/documents/share/{token}/download/{documentId}', [\App\Http\Controllers\DocumentShareController::class, 'download'])->name('documents.share.download');
Route::post('/documents/share/{token}/report-abuse', [\App\Http\Controllers\DocumentShareController::class, 'reportAbuse'])->name('documents.share.report-abuse');

// Public Profile Share Routes (no auth required)
Route::get('/profile/share/{token}', [\App\Http\Controllers\ProfileShareController::class, 'view'])->name('profile.share.view');
Route::post('/profile/share/{token}/download', [\App\Http\Controllers\ProfileShareController::class, 'downloadZip'])->name('profile.share.download');

// OCR Accuracy Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale'])->prefix('ocr')->name('ocr.')->group(function () {
    Route::get('/accuracy', [\App\Http\Controllers\OcrAccuracyController::class, 'index'])->name('accuracy.index');
    Route::get('/accuracy/{id}', [\App\Http\Controllers\OcrAccuracyController::class, 'show'])->name('accuracy.show');
    Route::get('/accuracy/export', [\App\Http\Controllers\OcrAccuracyController::class, 'export'])->name('accuracy.export');
    Route::get('/accuracy/stats', [\App\Http\Controllers\OcrAccuracyController::class, 'stats'])->name('accuracy.stats');
});

// Verification Appeals Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale'])->prefix('verification/appeals')->name('verification.appeals.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VerificationAppealController::class, 'index'])->name('index')->middleware('role:super_admin|admin|verifier');
    Route::get('/my-appeals', [\App\Http\Controllers\VerificationAppealController::class, 'myAppeals'])->name('my-appeals');
    Route::get('/create/{verification}', [\App\Http\Controllers\VerificationAppealController::class, 'create'])->name('create');
    Route::post('/{verification}', [\App\Http\Controllers\VerificationAppealController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\VerificationAppealController::class, 'show'])->name('show');
    Route::post('/{id}/assign', [\App\Http\Controllers\VerificationAppealController::class, 'assign'])->name('assign')->middleware('role:super_admin|admin');
    Route::post('/{id}/review', [\App\Http\Controllers\VerificationAppealController::class, 'review'])->name('review')->middleware('role:super_admin|admin|verifier');
    Route::post('/{id}/withdraw', [\App\Http\Controllers\VerificationAppealController::class, 'withdraw'])->name('withdraw');
    Route::get('/{id}/evidence/{fileIndex}', [\App\Http\Controllers\VerificationAppealController::class, 'downloadEvidence'])->name('download-evidence');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'setlocale'])
    ->group(function () {
        Route::get('/admin/waitlist', [WaitlistAdminController::class, 'index'])->name('admin.waitlist');
        Route::patch('/admin/waitlist/{waitlist}', [WaitlistAdminController::class, 'update'])->name('admin.waitlist.update');
    });



// 🚪 Subscription page (accessible without active subscription but still requires login)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified','setlocale'])
    ->group(function () {
        Route::get('/subscription', SubscriptionPage::class)->name('subscription.page');

    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');

    Route::get('/subscription/cancel', function () {
        return redirect()->route('subscription.page')->with('failed', 'Payment cancelled or failed!');
    })->name('subscription.cancel');

    // Admin Subscription Dashboard (role check done in component)
    Route::get('/admin/subscriptions', \App\Livewire\Admin\SubscriptionAdmin::class)->name('admin.subscriptions');
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
    // Renamed to main-dashboard to avoid conflict with financial.dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('main-dashboard');

    
    
    
    Route::get('/manage-document', ManageDocument::class)->name('manage-document');
    
    // Profile photo upload (standard Laravel upload - works without tmpfile())
    Route::post('/profile/photo/upload', [\App\Http\Controllers\ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::post('/profile/photo/remove', [\App\Http\Controllers\ProfilePhotoController::class, 'remove'])->name('profile.photo.remove');
  
  	Route::prefix('forum')->name('forum.')->group(function () {
        // Use our custom routes file that uses our overridden components
        Route::middleware([\App\Http\Middleware\ResolveForumParameters::class])->group(function () {
            require base_path('routes/forum-livewire.php');
        });
    });
  
    Route::get('/forums', [ManageDocumentController::class, 'forums'])->name('forum');
    Route::get('/documents', [ManageDocumentController::class, 'documents'])->name('documents');
    // Mental Health & Wellness Support Routes
    Route::prefix('mental-health')->name('mental-health.')->group(function () {
        Route::get('/', \App\Livewire\MentalHealth\MentalHealthDashboard::class)->name('dashboard');
        Route::get('/therapists', \App\Livewire\MentalHealth\TherapistDirectory::class)->name('therapists');
        Route::get('/book-session', \App\Livewire\MentalHealth\BookSession::class)->name('book-session');
        Route::get('/book-session/{therapistId}', \App\Livewire\MentalHealth\BookSession::class)->name('book-session.therapist');
        Route::get('/sessions', function() {
            return redirect()->route('mental-health.dashboard');
        })->name('sessions');
        Route::get('/crisis-support', \App\Livewire\MentalHealth\CrisisSupport::class)->name('crisis');
        Route::get('/mood-tracking', \App\Livewire\MentalHealth\MoodTracking::class)->name('mood-tracking');
        Route::get('/resources', \App\Livewire\MentalHealth\ResourcesLibrary::class)->name('resources');
        Route::get('/resources/{id}', \App\Livewire\MentalHealth\ViewResource::class)->name('resources.view');
        
        // Admin Routes - Use auth middleware and check role in component
        Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
            Route::get('/dashboard', \App\Livewire\MentalHealth\Admin\AdminDashboard::class)->name('dashboard');
            
            // Therapist Management
            Route::get('/therapists', \App\Livewire\MentalHealth\Admin\TherapistManagement::class)->name('therapists');
            Route::get('/therapists/create', \App\Livewire\MentalHealth\Admin\TherapistForm::class)->name('therapists.create');
            Route::get('/therapists/{id}/edit', \App\Livewire\MentalHealth\Admin\TherapistForm::class)->name('therapists.edit');
            
            // Resource Management
            Route::get('/resources', \App\Livewire\MentalHealth\Admin\ResourceManagement::class)->name('resources');
            Route::get('/resources/create', \App\Livewire\MentalHealth\Admin\ResourceForm::class)->name('resources.create');
            Route::get('/resources/{id}/edit', \App\Livewire\MentalHealth\Admin\ResourceForm::class)->name('resources.edit');
            
            Route::get('/sessions', \App\Livewire\MentalHealth\Admin\UserSessionManagement::class)->name('sessions');
            Route::get('/analytics', \App\Livewire\MentalHealth\Admin\UserAnalytics::class)->name('analytics');
        });
    });
    Route::get('/pension-investment-advice', \App\Livewire\FinancialPlanning\PensionInvestmentAdvice::class)->middleware('auth')->name('pension-investment-advice');
    Route::get('/training', [ManageDocumentController::class, 'training'])->name('training');
    Route::get('/weather', [ManageDocumentController::class, 'weather'])->name('weather');
    Route::get('/review', [ManageDocumentController::class, 'review'])->name('review');
  
    Route::get('/certificate-types', [CertificateTypeController::class, 'index'])->name('certificate-types.index');
    Route::patch('/certificate-type/{id}/toggle', [CertificateTypeController::class, 'toggleActive'])->name('certificate-type.toggle');
  
  	Route::get('/documents', [CareerHistoryController::class, 'index'])->name('documents');
  	// Documents show route - must come before career-history route to avoid conflict
  	Route::get('/documents/{id}', [CareerHistoryController::class, 'show'])->name('documents.show');
  	// Share Profile route for web (uses session auth)
  	Route::get('/share-profile', [CareerHistoryController::class, 'shareProfile'])->name('share-profile');
  	// Career History routes - specific routes must come before parameterized routes
  	Route::get('/career-history/manage', \App\Livewire\CareerHistory\CareerHistoryManager::class)->name('career-history.manage');
  	Route::get('/career-history/manage/{userId}', \App\Livewire\CareerHistory\CareerHistoryManager::class)->name('career-history.manage.user');
  	Route::get('/career-history/sea-service-report', [\App\Http\Controllers\CareerHistory\SeaServiceReportController::class, 'view'])->name('career-history.sea-service-report');
  	Route::get('/career-history/sea-service-report/{userId}', [\App\Http\Controllers\CareerHistory\SeaServiceReportController::class, 'view'])->name('career-history.sea-service-report.user');
  	Route::get('/career-history/sea-service-report/download', [\App\Http\Controllers\CareerHistory\SeaServiceReportController::class, 'download'])->name('career-history.sea-service-report.download');
  	Route::get('/career-history/sea-service-report/{userId}/download', [\App\Http\Controllers\CareerHistory\SeaServiceReportController::class, 'download'])->name('career-history.sea-service-report.user.download');
  	// Legacy route - redirect to manage
  	Route::get('/career-history/{userId?}', function($userId = null) {
  		if ($userId) {
  			return redirect()->route('career-history.manage.user', ['userId' => $userId]);
  		}
  		return redirect()->route('career-history.manage');
  	})->name('career-history');
    Route::get('/certificate-type/{id}/issuers', [CareerHistoryController::class, 'getIssuersByType']);
    Route::post('/documents/scan', [CareerHistoryController::class, 'scan'])->name('documents.scan');
    Route::post('/documents/{id}/retry-ocr', [CareerHistoryController::class, 'retryOcr'])->name('documents.retry-ocr');
    Route::post('/career-history', [CareerHistoryController::class, 'store'])->name('career-history.store');
  	Route::post('/documents/toggle-share', [CareerHistoryController::class, 'toggleShare'])->name('documents.toggleShare');
	
  	// Legacy route - kept for backward compatibility but redirects to documents.show
  	Route::get('/career-history/{id}', function($id) {
  		return redirect()->route('documents.show', $id);
  	})->name('career-history.show');
  	Route::get('/career-history/documents/{id}/edit', [CareerHistoryController::class, 'getDocumentForEdit'])->name('career-history.documents.edit');
    Route::put('/career-history/{id}', [CareerHistoryController::class, 'update'])->name('career-history.update');

  	Route::post('/admin/documents/{document}/verify', [CocCheckerController::class, 'verify']);
    Route::patch('/admin/documents/{document}/status', [CareerHistoryController::class, 'updateStatus']);
    
    // Document Verification Routes
    Route::post('/documents/{document}/request-verification', [\App\Http\Controllers\DocumentVerificationController::class, 'requestVerification'])->name('documents.request-verification');
    Route::post('/documents/{document}/verify', [\App\Http\Controllers\DocumentVerificationController::class, 'verify'])->name('documents.verify');
    Route::get('/verification/queue', [\App\Http\Controllers\DocumentVerificationController::class, 'queue'])->name('verification.queue');
    
    // Share Template Routes - Legacy API routes (kept for backward compatibility)
    Route::get('/share-templates/api', [\App\Http\Controllers\ShareTemplateController::class, 'index'])->name('share-templates.api');
    Route::post('/share-templates/{shareTemplate}/apply', [\App\Http\Controllers\ShareTemplateController::class, 'apply'])->name('share-templates.apply');
    
    // Admin Document Approval
    Route::get('/admin/documents/approval', \App\Livewire\Documents\Admin\DocumentApproval::class)->name('admin.documents.approval');
  
    Route::post('/documents/share', [DocumentController::class, 'share'])->name('documents.share'); // Legacy email sharing
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('/profile/share', [ProfileController::class, 'share'])->name('profile.share'); // Legacy email sharing

    // Secure document access routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/{document}/signed-url', [\App\Http\Controllers\DocumentDownloadController::class, 'signedUrl'])->name('signed-url');
        Route::get('/{document}/download', [\App\Http\Controllers\DocumentDownloadController::class, 'download'])->name('download');
        Route::get('/{document}/view', [\App\Http\Controllers\DocumentDownloadController::class, 'view'])->name('view');
        Route::get('/{document}/versions', [\App\Http\Controllers\CareerHistoryController::class, 'showVersions'])->name('versions.show');
        Route::post('/{document}/restore-version/{version}', [\App\Http\Controllers\CareerHistoryController::class, 'restoreVersion'])->name('restore-version');
        Route::get('/{document}/verification-certificate', [\App\Http\Controllers\CareerHistoryController::class, 'downloadVerificationCertificate'])->name('verification-certificate');
        Route::get('/versions/{version}/download', [\App\Http\Controllers\DocumentDownloadController::class, 'downloadVersion'])->name('versions.download');
    });

    // Crewdentials integration routes
    Route::prefix('crewdentials')->name('crewdentials.')->group(function () {
        Route::get('/check-account', [\App\Http\Controllers\CrewdentialsController::class, 'checkAccount'])->name('check-account');
        Route::post('/consent', [\App\Http\Controllers\CrewdentialsController::class, 'storeConsent'])->name('consent.store');
        Route::get('/consent', [\App\Http\Controllers\CrewdentialsController::class, 'getConsent'])->name('consent.get');
        Route::post('/import', [\App\Http\Controllers\CrewdentialsController::class, 'importDocuments'])->name('import');
        Route::post('/request-verification', [\App\Http\Controllers\CrewdentialsController::class, 'requestVerification'])->name('request-verification');
        Route::get('/documents/needing-category', [\App\Http\Controllers\CrewdentialsController::class, 'getDocumentsNeedingCategory'])->name('documents.needing-category');
        Route::post('/documents/{document}/assign-category', [\App\Http\Controllers\CrewdentialsController::class, 'assignCategory'])->name('documents.assign-category');
        Route::get('/sync/failed', [\App\Http\Controllers\CrewdentialsController::class, 'getFailedSyncs'])->name('sync.failed');
        Route::post('/sync/{sync}/retry', [\App\Http\Controllers\CrewdentialsController::class, 'retrySync'])->name('sync.retry');
        Route::post('/consent/withdraw', [\App\Http\Controllers\CrewdentialsController::class, 'withdrawConsent'])->name('consent.withdraw');
        Route::post('/profile-preview', [\App\Http\Controllers\CrewdentialsController::class, 'getProfilePreview'])->name('profile-preview');
    });

    // Crewdentials webhook (public, no auth required)
    Route::post('/crewdentials/webhook', [\App\Http\Controllers\CrewdentialsController::class, 'webhook'])->name('crewdentials.webhook');

    // New token-based sharing routes
    Route::prefix('shares')->name('shares.')->group(function () {
        // Document shares
        Route::get('/documents/create', [\App\Http\Controllers\DocumentShareController::class, 'create'])->name('documents.create');
        Route::get('/documents', [\App\Http\Controllers\DocumentShareController::class, 'index'])->name('documents.index');
        Route::get('/documents/analytics', \App\Livewire\Documents\ShareAnalytics::class)->name('documents.analytics');
        Route::post('/documents', [\App\Http\Controllers\DocumentShareController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{share}', [\App\Http\Controllers\DocumentShareController::class, 'revoke'])->name('documents.revoke');
        Route::post('/documents/{share}/resend', [\App\Http\Controllers\DocumentShareController::class, 'resend'])->name('documents.resend');
        Route::post('/documents/{share}/qr-code', [\App\Http\Controllers\DocumentShareController::class, 'generateQrCode'])->name('documents.qr-code');
        Route::get('/documents/view/{token}', [\App\Http\Controllers\DocumentShareController::class, 'view'])->name('documents.view');

        // Profile shares
        Route::get('/profile', [\App\Http\Controllers\ProfileShareController::class, 'index'])->name('profile.index');
        Route::post('/profile', [\App\Http\Controllers\ProfileShareController::class, 'store'])->name('profile.store');
        Route::delete('/profile/{share}', [\App\Http\Controllers\ProfileShareController::class, 'revoke'])->name('profile.revoke');
        Route::post('/profile/{share}/qr-code', [\App\Http\Controllers\ProfileShareController::class, 'generateQrCode'])->name('profile.qr-code');
        Route::get('/profile/view/{token}', [\App\Http\Controllers\ProfileShareController::class, 'view'])->name('profile.view');
    });

  
  	Route::get('/qrcode', [QRCodeController::class, 'generate'])->name('qrcode.generate');
  
  	Route::get('/profile', Profile::class)->name('profile');
    Route::get('/change-password', UpdatePasswordForm::class)->name('profile.password');
    

	
	
  
  	Route::get('/purchase-history', PurchaseHistory::class)->name('purchase.history');
	Route::get('/users', UserList::class)->name('users.index');
  	Route::get('/itinerary-system', ItinerarySystem::class)->name('itinerary.system');
	
    Route::get('/career-history/issuers', [CareerHistoryController::class, 'getIssuers'])->name('career-history.issuers');

    Route::get('/certificate-types/create', [CertificateTypeController::class, 'create'])->name('certificate-type.create');
    Route::post('/certificate-types', [CertificateTypeController::class, 'store'])->name('certificate-type.store');
    Route::get('certificate-types/{id}/edit', [CertificateTypeController::class, 'edit'])->name('certificate-type.edit');
    Route::put('certificate-types/{id}', [CertificateTypeController::class, 'update'])->name('certificate-type.update');
  	Route::delete('/certificate-type/{id}', [CertificateTypeController::class, 'destroy'])->name('certificate-type.destroy');

    Route::get('/certificate-issuers', CertificateIssuerIndex::class)->name('certificate.issuers.index');
  
  	Route::get('/legal-support', LegalSupport::class)->name('legal-support.index');
  
  	Route::get('/training-resources', TrainingResources::class)->name('training.resources');
  	
 	// Training & Resources Module Routes
 	Route::get('/training/courses', CourseDiscovery::class)->name('training.courses');
 	Route::get('/training/certification/{slug}', CertificationDetail::class)->name('training.certification.detail');
 	Route::get('/training/course/{courseId}', \App\Livewire\Training\CourseDetail::class)->name('training.course.detail');
 	Route::get('/training/my-certifications', UserCertificationDashboard::class)->name('training.my-certifications');
 	Route::get('/training/provider-portal', ProviderPortal::class)->name('training.provider.portal');
 	Route::get('/training/review/{courseId}', \App\Livewire\Training\SubmitReview::class)->name('training.review.submit');
 	Route::get('/training/career-pathway/{id?}', \App\Livewire\Training\CareerPathwayView::class)->name('training.career-pathway');
 	Route::get('/training/schedule-calendar', \App\Livewire\Training\CourseScheduleCalendar::class)->name('training.schedule.calendar');
 	Route::get('/training/schedule-calendar/provider/{provider}', \App\Livewire\Training\CourseScheduleCalendar::class)->name('training.schedule.calendar.provider');
  	
  	// Training Admin Routes (Super Admin Only)
  	Route::middleware(['auth'])->group(function () {
  	    Route::get('/training/admin', TrainingAdminDashboard::class)->name('training.admin.dashboard');
  	    
  	    // Categories
  	    Route::get('/training/admin/categories', ManageCategories::class)->name('training.admin.categories');
  	    
  	    // Certifications
  	    Route::get('/training/admin/certifications', ManageCertifications::class)->name('training.admin.certifications');
  	    Route::get('/training/admin/certifications/create', CertificationForm::class)->name('training.admin.certifications.create');
  	    Route::get('/training/admin/certifications/{id}/edit', CertificationForm::class)->name('training.admin.certifications.edit');
  	    
 	    // Providers
 	    Route::get('/training/admin/providers', ManageProviders::class)->name('training.admin.providers');
 	    Route::get('/training/admin/providers/create', ProviderForm::class)->name('training.admin.providers.create');
 	    Route::get('/training/admin/providers/{id}/edit', ProviderForm::class)->name('training.admin.providers.edit');
 	    
 	    // Courses
 	    Route::get('/training/admin/courses', ManageCourses::class)->name('training.admin.courses');
 	    Route::get('/training/admin/courses/create', \App\Livewire\Training\Admin\CourseForm::class)->name('training.admin.courses.create');
 	    Route::get('/training/admin/courses/{id}/edit', \App\Livewire\Training\Admin\CourseForm::class)->name('training.admin.courses.edit');
 	    Route::get('/training/admin/reviews', ManageReviews::class)->name('training.admin.reviews');
  	});

	Route::get('/roles', RoleIndex::class)->name('roles.index');

	Route::get('/master-data', \App\Livewire\Admin\MasterDataManage::class)->name('master-data.index');

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
    Route::get('/work-schedules', ScheduleManager::class)->name('work-schedules.index');
    Route::get('/captain-dashboard', CaptainDashboard::class)->name('captain-dashboard.index');
    Route::get('/industry-review', IndustryReviewIndex::class)->name('industryreview.index');

    // Industry Review Create Pages (must come before show pages to avoid conflicts)
    Route::get('/industry-review/yachts/reviews/create', \App\Livewire\IndustryReview\YachtReviewCreate::class)->name('yacht-reviews.create');
    Route::get('/industry-review/marinas/reviews/create', \App\Livewire\IndustryReview\MarinaReviewCreate::class)->name('marina-reviews.create');

    // Industry Review Management - Specific routes must come before slug routes
    Route::get('/industry-review/yachts', \App\Livewire\IndustryReview\YachtManage::class)->name('industryreview.yachts.manage');
    Route::get('/industry-review/yachts/create', \App\Livewire\IndustryReview\YachtForm::class)->name('industryreview.yachts.create');
    Route::get('/industry-review/yachts/{id}/edit', \App\Livewire\IndustryReview\YachtForm::class)->name('industryreview.yachts.edit')->where('id', '[0-9]+');
    Route::get('/industry-review/yachts/{id}/members', \App\Livewire\IndustryReview\YachtMembers::class)->name('industryreview.yachts.members')->where('id', '[0-9]+');
    
    Route::get('/industry-review/marinas', \App\Livewire\IndustryReview\MarinaManage::class)->name('industryreview.marinas.manage');
    Route::get('/industry-review/marinas/create', \App\Livewire\IndustryReview\MarinaForm::class)->name('industryreview.marinas.create');
    Route::get('/industry-review/marinas/{id}/edit', \App\Livewire\IndustryReview\MarinaForm::class)->name('industryreview.marinas.edit')->where('id', '[0-9]+');

    // Industry Review Index Pages
    Route::get('/industry-review/yachts/list', \App\Livewire\IndustryReview\YachtReviewIndex::class)->name('yacht-reviews.index');
    Route::get('/industry-review/marinas/list', \App\Livewire\IndustryReview\MarinaReviewIndex::class)->name('marina-reviews.index');
    Route::get('/industry-review/my-reviews', \App\Livewire\IndustryReview\MyReviews::class)->name('my-reviews.index');

    // Industry Review Show Pages - Must come after specific routes
    Route::get('/industry-review/yachts/{slug}', \App\Livewire\IndustryReview\YachtReviewShow::class)->name('yacht-reviews.show');
    Route::get('/industry-review/yachts/{slug}/gallery', \App\Livewire\IndustryReview\YachtGallery::class)->name('yacht-reviews.gallery');
    Route::get('/industry-review/marinas/{slug}', \App\Livewire\IndustryReview\MarinaReviewShow::class)->name('marina-reviews.show');
    Route::get('/industry-review/marinas/{slug}/gallery', \App\Livewire\IndustryReview\MarinaGallery::class)->name('marina-reviews.gallery');
    
    // Contractor Management Routes (must come before {slug} routes)
    Route::get('/industry-review/contractors', \App\Livewire\IndustryReview\ContractorManage::class)->name('industryreview.contractors.manage');
    Route::get('/industry-review/contractors/create', \App\Livewire\IndustryReview\ContractorForm::class)->name('industryreview.contractors.create');
    Route::get('/industry-review/contractors/{id}/edit', \App\Livewire\IndustryReview\ContractorForm::class)->name('industryreview.contractors.edit')->where('id', '[0-9]+');
    
    // Broker Management Routes (must come before {slug} routes)
    Route::get('/industry-review/brokers', \App\Livewire\IndustryReview\BrokerManage::class)->name('industryreview.brokers.manage');
    Route::get('/industry-review/brokers/create', \App\Livewire\IndustryReview\BrokerForm::class)->name('industryreview.brokers.create');
    Route::get('/industry-review/brokers/{id}/edit', \App\Livewire\IndustryReview\BrokerForm::class)->name('industryreview.brokers.edit')->where('id', '[0-9]+');
    
    Route::get('/industry-review/restaurants', \App\Livewire\IndustryReview\RestaurantManage::class)->name('industryreview.restaurants.manage');
    Route::get('/industry-review/restaurants/create', \App\Livewire\IndustryReview\RestaurantForm::class)->name('industryreview.restaurants.create');
    Route::get('/industry-review/restaurants/{id}/edit', \App\Livewire\IndustryReview\RestaurantForm::class)->name('industryreview.restaurants.edit')->where('id', '[0-9]+');

    // Contractor Reviews (Listing page) - must come after management routes
    Route::get('/industry-review/contractors/list', \App\Livewire\IndustryReview\ContractorIndex::class)->name('contractor-reviews.index');
    Route::get('/industry-review/contractors/reviews/create', \App\Livewire\IndustryReview\ContractorReviewCreate::class)->name('contractor-reviews.create');
    Route::get('/industry-review/contractors/{slug}', \App\Livewire\IndustryReview\ContractorReviewShow::class)->name('contractor-reviews.show');
    Route::get('/industry-review/contractors/{slug}/gallery', \App\Livewire\IndustryReview\ContractorGallery::class)->name('contractor-reviews.gallery');

    // Broker Reviews (Listing page) - must come after management routes
    Route::get('/industry-review/brokers/list', \App\Livewire\IndustryReview\BrokerIndex::class)->name('broker-reviews.index');
    Route::get('/industry-review/brokers/reviews/create', \App\Livewire\IndustryReview\BrokerReviewCreate::class)->name('broker-reviews.create');
    Route::get('/industry-review/brokers/{slug}', \App\Livewire\IndustryReview\BrokerReviewShow::class)->name('broker-reviews.show');
    Route::get('/industry-review/brokers/{slug}/gallery', \App\Livewire\IndustryReview\BrokerGallery::class)->name('broker-reviews.gallery');

    // Restaurant Reviews (Listing page)
    Route::get('/industry-review/restaurants/list', \App\Livewire\IndustryReview\RestaurantIndex::class)->name('restaurant-reviews.index');
    
    // Admin & Advanced Features
    Route::get('/industry-review/moderation', \App\Livewire\IndustryReview\ModerationDashboard::class)->name('industryreview.moderation');
    Route::get('/industry-review/statistics', \App\Livewire\IndustryReview\StatisticsDashboard::class)->name('industryreview.statistics');
    Route::get('/industry-review/brokers/compare', \App\Livewire\IndustryReview\BrokerComparison::class)->name('broker-reviews.compare');
    Route::get('/industry-review/location/{location}', \App\Livewire\IndustryReview\LocationResources::class)->name('industryreview.location');
    Route::get('/industry-review/education', \App\Livewire\IndustryReview\EducationalContent::class)->name('industryreview.education');
    Route::get('/industry-review/restaurants/reviews/create', \App\Livewire\IndustryReview\RestaurantReviewCreate::class)->name('restaurant-reviews.create');
    Route::get('/industry-review/restaurants/{slug}', \App\Livewire\IndustryReview\RestaurantReviewShow::class)->name('restaurant-reviews.show');
    Route::get('/industry-review/restaurants/{slug}/gallery', \App\Livewire\IndustryReview\RestaurantGallery::class)->name('restaurant-reviews.gallery');

    // Job Board Routes
    Route::prefix('job-board')->name('job-board.')->group(function () {
        Route::get('/', \App\Livewire\JobBoard\JobBoardIndex::class)->name('index');
        Route::get('/browse', \App\Livewire\JobBoard\BrowseJobs::class)->name('browse');
        Route::get('/post', \App\Livewire\JobBoard\PostJob::class)->name('post');
        Route::get('/post/{id}', \App\Livewire\JobBoard\PostJob::class)->name('post.edit');
        Route::get('/jobs/{id}', \App\Livewire\JobBoard\JobDetail::class)->name('detail');
        Route::get('/jobs/{id}/apply', \App\Livewire\JobBoard\ApplyJob::class)->name('apply');
        Route::get('/jobs/{id}/apply-temporary', \App\Livewire\JobBoard\ApplyTemporaryWork::class)->name('apply-temporary');
        Route::get('/applications', \App\Livewire\JobBoard\ManageApplications::class)->name('applications');
        Route::get('/my-applications', \App\Livewire\JobBoard\MyApplications::class)->name('my-applications');
        
        // Temporary Work
        Route::get('/temporary-work', \App\Livewire\JobBoard\TemporaryWorkMarketplace::class)->name('temporary-work');
        Route::get('/post-temporary-work', \App\Livewire\JobBoard\PostTemporaryWork::class)->name('post-temporary-work');
        
        // Crew Availability
        Route::get('/available-crew', \App\Livewire\JobBoard\AvailableCrewSearch::class)->name('available-crew');
        Route::get('/availability-settings', \App\Livewire\JobBoard\CrewAvailabilitySettings::class)->name('availability-settings');
        
        // Vessel Verification
        Route::get('/verify', \App\Livewire\JobBoard\VesselVerificationRequest::class)->name('verify');
        
        // Preferred Crew
        Route::get('/preferred-crew', \App\Livewire\JobBoard\PreferredCrewList::class)->name('preferred-crew');
        
        // Ratings
        Route::get('/ratings/{bookingId?}', \App\Livewire\JobBoard\JobRatings::class)->name('ratings');
        
        // Temporary Work Bookings Management
        Route::get('/bookings', \App\Livewire\JobBoard\TemporaryWorkBookingManagement::class)->name('bookings');
        Route::get('/book-crew/{crewUserId}', \App\Livewire\JobBoard\BookTemporaryWork::class)->name('book-crew');
        Route::get('/book-crew/{crewUserId}/job/{jobPostId}', \App\Livewire\JobBoard\BookTemporaryWork::class)->name('book-crew-from-job');
        
        // Admin Panel - role check is done in component
        Route::get('/admin', \App\Livewire\JobBoard\JobBoardAdmin::class)->name('admin');
    });

});


Route::get('/verify-user/{id}', [VerificationController::class, 'verify'])
    ->name('user.verify')
    ->middleware('signed');

// Document Verification API Demo Page
Route::get('/document-verification-demo', function () {
    return response()->file(public_path('document-verification-demo.html'));
})->name('document-verification-demo');
    