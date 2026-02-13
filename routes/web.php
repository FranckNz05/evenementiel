<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\ScanController;
use App\Http\Controllers\AccessCodeController;
use App\Http\Controllers\OrganizerProfileController;
use App\Http\Controllers\Organizer\AnalyticsController;
use App\Http\Controllers\Organizer\TicketController;
// use App\Http\Controllers\Organizer\StatsController;
use App\Http\Controllers\Organizer\OrganizerDashboardController;
use App\Http\Controllers\Organizer\PaymentController as OrganizerPaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\OrganizerRequestController;
use App\Http\Controllers\QrScanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\API\InfluencerController as ApiInfluencerController;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Ici sont définies toutes les routes web de l'application, organisées par section.
*/

// Accueil & Authentification
Route::get('/', [HomeController::class, 'index'])->name('home');
// Servir les fichiers de storage avec en-têtes CORS pour Flutter Web
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization',
    ]);
})->where('path', '.*');
Route::get('/docs', function() { return view('docs.index'); })->name('docs.index');

// Événements personnalisés (formules)
use App\Http\Controllers\CustomEventsController;
Route::middleware(['auth'])->group(function () {
    Route::get('/evenements-personnalises', [CustomEventsController::class, 'index'])->name('custom-offers.index');
    Route::get('/evenements-personnalises/paiement', [CustomEventsController::class, 'payment'])->name('custom-offers.payment');
    Route::post('/evenements-personnalises/paiement', [CustomEventsController::class, 'processPayment'])->name('custom-offers.payment.process');
    Route::get('/evenements-personnalises/confirmation', [CustomEventsController::class, 'confirmation'])->name('custom-offers.confirmation');

    // Invitations et Export CSV des invités pour événements personnalisés
    Route::post('custom-events/{event}/invitations/send-all', [App\Http\Controllers\CustomEventInvitationController::class, 'sendAll'])
        ->name('custom-events.invitations.send-all');
    Route::get('custom-events/{event}/guests/export', [App\Http\Controllers\CustomEventExportController::class, 'exportGuests'])
        ->name('custom-events.guests.export');
    Route::get('custom-events/invitations/csv-template', [App\Http\Controllers\CustomEventInvitationController::class, 'downloadCsvTemplate'])
        ->name('custom-events.invitations.csv-template');


    // Wizard de création événements personnalisés
    Route::prefix('custom-events/wizard')->name('custom-events.wizard.')->group(function () {
        Route::get('step1', [App\Http\Controllers\CustomEventWizardController::class, 'step1'])->name('step1');
        Route::post('step1', [App\Http\Controllers\CustomEventWizardController::class, 'storeStep1'])->name('step1.store');
        Route::get('step2', [App\Http\Controllers\CustomEventWizardController::class, 'step2'])->name('step2');
        Route::post('step2', [App\Http\Controllers\CustomEventWizardController::class, 'storeStep2'])->name('step2.store');
        Route::get('step3', [App\Http\Controllers\CustomEventWizardController::class, 'step3'])->name('step3');
        Route::post('step3', [App\Http\Controllers\CustomEventWizardController::class, 'storeStep3'])->name('step3.store');
        Route::get('complete', [App\Http\Controllers\CustomEventWizardController::class, 'complete'])->name('complete');
    });
});

// Pages statiques - Routes supprimées car elles sont définies plus bas avec le contrôleur

// COMMENTÉ - Groupe organisateur en double (voir ligne 1053+ pour le groupe principal)

// Routes pour la création d'événements - accessible à tous les utilisateurs authentifiés
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/events/create', [App\Http\Controllers\Organizer\EventController::class, 'create'])->name('events.create');
    Route::get('/events/select-type', [App\Http\Controllers\Organizer\EventController::class, 'selectType'])->name('events.select-type');
});

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Événements
    Route::get('/events', [App\Http\Controllers\Organizer\EventController::class, 'index'])->name('events.index');
    // Route /events/create est définie dans le groupe accessible à tous (ligne 111)
    Route::post('/events', [App\Http\Controllers\Organizer\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [App\Http\Controllers\Organizer\EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [App\Http\Controllers\Organizer\EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [App\Http\Controllers\Organizer\EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [App\Http\Controllers\Organizer\EventController::class, 'destroy'])->name('events.destroy');

    // Paiements
    Route::get('/payments', [OrganizerPaymentController::class, 'index'])->name('payments.index');

    // Scans QR
    Route::get('/scans', [ScanController::class, 'index'])->name('scans.index');

    // Codes d'accès
    Route::get('/access-codes', [AccessCodeController::class, 'index'])->name('access-codes.index');

    // Retraits
    Route::get('/withdrawals', [App\Http\Controllers\Organizer\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/create', [App\Http\Controllers\Organizer\WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [App\Http\Controllers\Organizer\WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/withdrawals/{withdrawal}/check-status', [App\Http\Controllers\Organizer\WithdrawalController::class, 'checkStatus'])->name('withdrawals.check-status');

    // Revenus organisateur
    Route::get('/revenue', [App\Http\Controllers\OrganizerRevenueController::class, 'dashboard'])->name('revenue.dashboard');
    Route::get('/revenue/api', [App\Http\Controllers\OrganizerRevenueController::class, 'getRevenueData'])->name('revenue.api');
    Route::get('/revenue/history', [App\Http\Controllers\OrganizerRevenueController::class, 'revenueHistory'])->name('revenue.history');
    Route::get('/revenue/export', [App\Http\Controllers\OrganizerRevenueController::class, 'exportRevenue'])->name('revenue.export');

    // Blogs
    Route::get('/blogs', [App\Http\Controllers\BlogController::class, 'organizerIndex'])->name('blogs.index');
    Route::get('/blogs/create', [App\Http\Controllers\BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [App\Http\Controllers\BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'destroy'])->name('blogs.destroy');
});

// Alias pour compatibilité avec les anciennes références
Route::middleware(['auth', 'role:organizer'])->group(function () {
    Route::get('/organizer/events', function() {
        return redirect()->route('organizer.events.index');
    })->name('organizer.events');
});

// Routes publiques
// Organizer Routes
Route::get('organizers', [App\Http\Controllers\OrganizerController::class, 'index'])->name('organizers.index');
Route::get('organizers/{organizer}', [App\Http\Controllers\OrganizerController::class, 'show'])
    ->name('organizers.show')
    ->where('organizer', '[a-zA-Z0-9_-]+');


// Organizer Follow Routes (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::post('organizers/{organizer}/follow', [App\Http\Controllers\OrganizerController::class, 'follow'])->name('organizers.follow');
    Route::delete('organizers/{organizer}/unfollow', [App\Http\Controllers\OrganizerController::class, 'unfollow'])->name('organizers.unfollow');
});

// Public Event Routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [App\Http\Controllers\EventController::class, 'index'])->name('index');
    
    // Routes spécifiques AVANT le wildcard
    Route::get('/promotions', [App\Http\Controllers\EventController::class, 'promotions'])->name('promotions');
    Route::get('/past', [App\Http\Controllers\EventController::class, 'past'])->name('past');
    
    // Route wildcard EN DERNIER
    Route::get('/{event:slug}', [App\Http\Controllers\EventController::class, 'show'])->name('show');
});

// Routes for direct-events
Route::get('/direct-events', [App\Http\Controllers\EventController::class, 'index'])->name('events.public');
Route::get('/direct-events/{event:slug}', [App\Http\Controllers\EventController::class, 'show'])->name('direct-events.show');

// Routes pour les partages d'événements
Route::post('/events/{event}/share', [App\Http\Controllers\ShareController::class, 'store'])->name('events.share');

// Routes pour les commentaires d'événements
Route::post('/events/{event}/comments', [App\Http\Controllers\EventCommentController::class, 'store'])->name('events.comments.store');
Route::delete('/events/{event}/comments/{comment}', [App\Http\Controllers\EventCommentController::class, 'destroy'])->name('events.comments.destroy');

// Influenceurs (web)
Route::middleware(['auth'])->group(function () {
    Route::post('/events/{event}/influencers/toggle-attend', [ApiInfluencerController::class, 'toggleAttend'])
        ->name('events.influencers.toggleAttend');
    Route::post('/influencers/{user}/toggle-follow', [ApiInfluencerController::class, 'toggleFollow'])
        ->name('influencers.toggleFollow');
});

// Pages influenceurs
Route::get('/events/{event}/influencers', [ApiInfluencerController::class, 'attendees'])->name('events.influencers');
Route::get('/influencers/{user}', [ApiInfluencerController::class, 'profile'])->name('influencers.show');

// Routes de connexion en mode maintenance (accessibles même si connecté, pour permettre la déconnexion des non-admins)
Route::get('admin-maintenance-login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'maintenanceLogin'])
    ->name('admin.maintenance.login');
Route::post('admin-maintenance-login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'maintenanceLoginStore'])
    ->name('admin.maintenance.login.submit');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::get('auth/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('auth.login');
    Route::post('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

    // Registration Routes
    Route::get('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
        ->name('register');
    Route::get('register-auth', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
        ->name('auth.register');
    Route::post('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

    // Social Authentication Routes
    Route::get('auth/{provider}', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])
        ->name('social.redirect');
    Route::get('auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])
        ->name('social.callback');

    // Password Reset Routes
    Route::get('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Email Verification via OTP (Web) - Accessible à tous (invités et authentifiés)
Route::get('verification/otp', [App\Http\Controllers\Auth\RegisterController::class, 'showVerificationForm'])
    ->name('verification.notice');
Route::post('verification/otp', [App\Http\Controllers\Auth\RegisterController::class, 'verifyOTP'])
    ->name('verify.otp');
Route::post('verification/otp/resend', [App\Http\Controllers\Auth\RegisterController::class, 'resendOTP'])
    ->name('resend.otp');

// Routes authentifiées pour la gestion du mot de passe et déconnexion
Route::middleware(['auth'])->group(function () {
    Route::get('mot-de-passe/confirmer', [App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');
    Route::post('mot-de-passe/confirmer', [App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    Route::put('mot-de-passe', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    // Forcer la création d'événement personnalisé via le wizard (redirige l'ancienne URL)
    Route::get('custom-events/create', function () {
        return redirect()->route('custom-events.wizard.step1');
    })->name('custom-events.create');

    // Profile - Routes déjà définies dans le groupe 'mon-profil' (ligne 1299) - doublons supprimés
    
    // Custom Events
    Route::resource('custom-events', App\Http\Controllers\CustomEventController::class);
    Route::post('custom-events/{event}/generate-checkin-url', [App\Http\Controllers\CustomEventController::class, 'generateCheckinUrl'])
        ->name('custom-events.generate-checkin-url');
    
    // Guest Management for Custom Events
    Route::prefix('custom-events/{event}')->name('custom-events.guests.')->group(function () {
        Route::post('guests', [App\Http\Controllers\CustomEventGuestController::class, 'store'])->name('store');
        Route::put('guests/{guest}', [App\Http\Controllers\CustomEventGuestController::class, 'update'])->name('update');
        Route::delete('guests/{guest}', [App\Http\Controllers\CustomEventGuestController::class, 'destroy'])->name('destroy');
        Route::post('guests/import', [App\Http\Controllers\CustomEventGuestController::class, 'import'])->name('import');
    });
    
    // Invitations for Custom Events
    Route::prefix('custom-events/{event}')->name('custom-events.invitations.')->group(function () {
        Route::post('invitations/{guest}', [App\Http\Controllers\CustomEventInvitationController::class, 'send'])->name('send');
    });
    
    // Guest Management (legacy)
    Route::post('events/{event}/guests', [App\Http\Controllers\GuestController::class, 'store'])
        ->name('guests.store');
});

// Route publique pour afficher l'invitation d'un événement personnalisé (accessible sans auth)
Route::get('/custom-events/invitation/{invitationLink}', [App\Http\Controllers\CustomEventController::class, 'showInvitation'])
    ->name('custom-events.invitation');

// Check-in temps réel (routes publiques - accessibles sans authentification)
Route::get('/checkin/{checkinUrl}', [App\Http\Controllers\CheckInController::class, 'realtime'])
    ->name('checkin.realtime');
Route::get('/checkin/{checkinUrl}/guests', [App\Http\Controllers\CheckInController::class, 'getGuests'])
    ->name('checkin.guests');
Route::get('/checkin/{checkinUrl}/search', [App\Http\Controllers\CheckInController::class, 'searchGuest'])
    ->name('checkin.search');
Route::post('/checkin/{checkinUrl}/manual', [App\Http\Controllers\CheckInController::class, 'manualCheckIn'])
    ->name('checkin.manual');
Route::post('/checkin/{checkinUrl}/scan', [App\Http\Controllers\CheckInController::class, 'scanQrCode'])
    ->name('checkin.scan');

// Route pour générer le QR code d'un événement personnalisé (authentifiée)
Route::middleware(['auth'])->group(function () {
    Route::get('/custom-events/{event}/qrcode', [App\Http\Controllers\CustomEventController::class, 'qrcode'])
        ->name('custom-events.qrcode');
});

Route::middleware(['auth'])->group(function () {

    // Custom Personal Events (privés, ex : mariage)
    Route::prefix('custom-personal-events')->name('custom-personal-events.')->group(function () {
    Route::get('create', [App\Http\Controllers\CustomPersonalEventController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\CustomPersonalEventController::class, 'store'])->name('store');
    Route::get('dashboard', [App\Http\Controllers\CustomPersonalEventController::class, 'dashboard'])->name('dashboard');
    Route::get('{url}', [App\Http\Controllers\CustomPersonalEventController::class, 'show'])->name('show');
    Route::post('{event}/guests', [App\Http\Controllers\CustomPersonalEventController::class, 'addGuest'])->name('guests.add');
    Route::post('{event}/guests/{guest}/cancel', [App\Http\Controllers\CustomPersonalEventController::class, 'cancelGuest'])->name('guests.cancel');
    Route::post('{event}/guests/{guest}/arrived', [App\Http\Controllers\CustomPersonalEventController::class, 'markArrived'])->name('guests.arrived');
    Route::post('{event}/update-code', [App\Http\Controllers\CustomPersonalEventController::class, 'updateCode'])->name('update-code');
    Route::get('edit/{event}', [App\Http\Controllers\CustomPersonalEventController::class, 'edit'])->name('edit');
    Route::put('edit/{event}', [App\Http\Controllers\CustomPersonalEventController::class, 'update'])->name('update');
    Route::delete('destroy/{event}', [App\Http\Controllers\CustomPersonalEventController::class, 'destroy'])->name('destroy');
    });

// Route publique pour la liste des invités d'un événement personnalisé (accessible sans auth)
Route::get('/custom-personal-events/public/{url}', [App\Http\Controllers\CustomPersonalEventController::class, 'showPublic'])->name('custom-personal-events.public');
    Route::put('guests/{guest}/status', [App\Http\Controllers\GuestController::class, 'updateStatus'])
        ->name('guests.update-status');
    
    // Invitations - COMMENTÉ: InvitationController n'existe pas
    /*
    Route::post('events/{event}/send-invitations', [App\Http\Controllers\InvitationController::class, 'send'])
        ->name('invitations.send');
    */
});

// Public Invitation Route - COMMENTÉ: InvitationController n'existe pas
/*
Route::get('/invitation/{token}', [App\Http\Controllers\InvitationController::class, 'show'])
    ->name('invitation.show');
Route::post('/invitation/{token}/respond', [App\Http\Controllers\InvitationController::class, 'respond'])
    ->name('invitation.respond');
*/

// Admin Routes - COMMENTÉ (voir ligne 838 pour le groupe principal avec préfixe 'Administrateur')
/*
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:Administrateur'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Events
        Route::resource('events', App\Http\Controllers\Admin\AdminEventController::class);
        Route::post('/events/{event}/approve', [App\Http\Controllers\Admin\AdminEventController::class, 'approve'])
            ->name('events.approve');
        Route::post('/events/{event}/reject', [App\Http\Controllers\Admin\AdminEventController::class, 'reject'])
            ->name('events.reject');
        Route::patch('/events/{event}/publish', [App\Http\Controllers\Admin\AdminEventController::class, 'publish'])
            ->name('events.publish');
        Route::patch('/events/{event}/unpublish', [App\Http\Controllers\Admin\AdminEventController::class, 'unpublish'])
            ->name('events.unpublish');
        Route::get('/events/pending', [App\Http\Controllers\Admin\AdminEventController::class, 'pending'])
            ->name('events.pending');
            
        // Users
        Route::get('/users', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminUserController::class, 'edit'])
            ->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'update'])
            ->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])
            ->name('users.destroy');
            
        // Organizer Requests
        Route::get('/organizer-requests', [App\Http\Controllers\Admin\OrganizerRequestController::class, 'index'])
            ->name('organizer-requests.index');
        Route::post('/organizer-requests/{id}/approve', [App\Http\Controllers\Admin\OrganizerRequestController::class, 'approve'])
            ->name('organizer-requests.approve');
        Route::post('/organizer-requests/{id}/reject', [App\Http\Controllers\Admin\OrganizerRequestController::class, 'reject'])
            ->name('organizer-requests.reject');
            
        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('reports.index');
        Route::post('/reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generate'])
            ->name('reports.generate');
            
        // Settings
        Route::get('/settings', [App\Http\Controllers\Admin\AdminDashboardController::class, 'settings'])
            ->name('settings');
        Route::post('/settings/update', [App\Http\Controllers\Admin\AdminDashboardController::class, 'updateSettings'])
            ->name('settings.update');
            
        // API routes pour les graphiques du dashboard
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/payments/methods', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getPaymentMethodsData'])
                ->name('payments.methods');
            Route::get('/payments/trends', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getTrendsData'])
                ->name('payments.trends');
        });
    });
*/

// Fallback Route
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Include organizer routes (already loaded by RouteServiceProvider)
// require __DIR__.'/organizer.php';

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Routes Admin - FIXED VERSION
|--------------------------------------------------------------------------
*/

// Authentification requise
Route::middleware(['auth'])->group(function () {
    // Gestion des événements
    Route::resource('events', App\Http\Controllers\CustomEventController::class);
    
    // Gestion des invités
    Route::post('events/{event}/guests', [App\Http\Controllers\GuestController::class, 'store'])
        ->name('guests.store');
    Route::put('guests/{guest}/status', [App\Http\Controllers\GuestController::class, 'updateStatus'])
        ->name('guests.update-status');
    
    // Envoi d'invitations - COMMENTÉ: InvitationController n'existe pas
    /*
    Route::post('events/{event}/send-invitations', [App\Http\Controllers\InvitationController::class, 'send'])
        ->name('invitations.send');
    */
});

// Lien public d'invitation - COMMENTÉ: InvitationController n'existe pas
/*
Route::get('/invitation/{token}', [App\Http\Controllers\InvitationController::class, 'show'])
    ->name('invitation.show');
Route::post('/invitation/{token}/respond', [App\Http\Controllers\InvitationController::class, 'respond'])
    ->name('invitation.respond');
*/


// COMMENTÉ - Groupe de routes admin en double (voir ligne 838 pour le groupe principal)
/*
Route::middleware(['auth', 'verified', 'role:Administrateur'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Routes spécifiques pour les événements (doivent être AVANT la resource route)
    Route::post('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('/events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    Route::get('/events/pending', [AdminEventController::class, 'pending'])->name('events.pending');
    Route::get('/events/pending/api', [AdminEventController::class, 'pendingApi'])->name('events.pending-api');
    
    
    // Resource routes pour les événements (après les routes spécifiques)
    Route::resource('events', AdminEventController::class);

    // API routes pour les données du dashboard admin
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/payments', [AdminPaymentController::class, 'getPaymentsData'])->name('payments');
        Route::get('/payments/methods', [AdminDashboardController::class, 'getPaymentMethodsData'])->name('payments.methods');
        Route::get('/payments/trends', [AdminDashboardController::class, 'getTrendsData'])->name('payments.trends');
        Route::get('/events', [AdminEventController::class, 'getEventsData'])->name('events');
        Route::get('/users', [AdminUserController::class, 'getUsersData'])->name('users');
    });

    // Routes pour les utilisateurs
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/data', [AdminUserController::class, 'getData'])->name('users.data');

    // Annonces
    Route::resource('announcements', AnnouncementController::class);
    Route::post('/announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');

    // Blogs
    Route::prefix('blogs')->name('blogs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Admin\BlogController::class, 'getData'])->name('data');
        Route::get('/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('store');
        Route::get('/{blog}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('edit');
        Route::put('/{blog}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('update');
        Route::delete('/{blog}', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('destroy');
    });

    // Cette route doit être en dernier pour éviter les conflits
    Route::get('/{blog:slug}', [BlogController::class, 'show'])->name('show');
});
*/

// Route pour l'abonnement à la newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Routes pour les clients authentifiés (nécessitent un compte vérifié)
Route::middleware(['auth', 'verified'])->group(function() {
    // Route pour afficher les tickets de l'utilisateur connecté
    Route::get('/mes-tickets', [\App\Http\Controllers\Client\TicketController::class, 'index'])
        ->name('client.tickets.index');
});

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
*/
/*
// COMMENTÉ - Routes en double (les mêmes routes existent déjà aux lignes 137-157)
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('inscription', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])
                ->name('inscription');
    Route::post('inscription', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

    Route::get('mot-de-passe/oublie', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
                ->name('password.request');
    Route::post('mot-de-passe/oublie', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('mot-de-passe/reinitialiser/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
                ->name('password.reset');
    Route::post('mot-de-passe/reinitialiser', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
                ->name('password.store');
});
*/

// Routes authentifiées
Route::middleware('auth')->group(function () {
    // COMMENTÉ - Routes de vérification email (Laravel Fortify gère déjà cela)
    /*
    Route::get('/email/verify', [VerificationController::class, 'showVerificationForm'])
        ->name('verification.notice');
    
    Route::post('/email/verify', [VerificationController::class, 'verify'])
        ->name('verification.verify');
    
    Route::post('/email/verify/resend', [VerificationController::class, 'resend'])
        ->name('verification.resend');
    */

    // Routes password déjà définies ligne 260-264 - doublons supprimés

    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

// Routes pour les commandes et réservations (nécessitent un compte vérifié)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::get('/orders/{order}/tickets-pdf', [OrderController::class, 'ticketsPdf'])->name('orders.tickets-pdf');
});

// Webhook pour les notifications de paiement
// Remplacer les deux routes existantes par :
// Payment routes


// Routes de paiement
Route::middleware(['auth', 'verified'])->group(function () {
    // Afficher le formulaire de paiement
    Route::get('/paiement/{order}/process', [PaymentController::class, 'process'])
        ->name('payments.process');
    
    // Historique
    Route::get('/payments/history', [PaymentController::class, 'history'])
        ->name('payments.history');
});

// Webhook pour les paiements (sans authentification)
Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])
    ->name('payment.callback');

// Routes pour les favoris (authentification requise)
Route::middleware('auth')->get('/favorites', [EventController::class, 'favorites'])->name('favorites.index');

// Routes pour devenir organisateur
Route::middleware(['auth'])->prefix('devenir-organisateur')->name('organizer-requests.')->group(function () {
    Route::get('/', [OrganizerRequestController::class, 'create'])->name('create');
    Route::post('/', [OrganizerRequestController::class, 'store'])->name('store');
    Route::get('/status', [OrganizerRequestController::class, 'status'])->name('status');
});

// Routes ADMIN pour gérer les demandes d'organisateurs
Route::prefix('admin/demandes-organisateurs')->name('admin.organizer-requests.')->middleware(['auth', 'role:Administrateur'])->group(function () {
    Route::get('/', [App\Http\Controllers\OrganizerRequestController::class, 'adminIndex'])->name('index');
    Route::post('/{organizerRequest}/approve', [App\Http\Controllers\OrganizerRequestController::class, 'approve'])->name('approve');
    Route::post('/{organizerRequest}/reject', [App\Http\Controllers\OrganizerRequestController::class, 'reject'])->name('reject');
});

// Les routes PawaPay sont maintenant dans routes/pawapay.php


// Payment routes
Route::middleware('auth')->group(function() {
    Route::get('/payment/process/{order}', [PaymentController::class, 'process'])
        ->name('payment.process');
});


/*
|--------------------------------------------------------------------------
| Routes Authentifiées
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Tableau de bord
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur - Routes déjà définies ligne 1296 - doublons supprimés

    // Gestion des reservations et réservations
    Route::middleware(['auth'])->group(function () {
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    });
});
    // Routes des reservations
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    // Routes de paiement
    Route::prefix('paiements')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments');
        Route::get('/methodes', [App\Http\Controllers\Admin\PaymentController::class, 'methods'])->name('methods');
        Route::get('/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/telecharger', [App\Http\Controllers\Admin\PaymentController::class, 'downloadInvoice'])->name('download');
    });

    // Interactions (commentaires, likes, vues)
    Route::prefix('interactions')->group(function () {
        Route::post('/events/{event}/comments', [App\Http\Controllers\EventCommentController::class, 'store'])->name('events.comments.store');
        Route::post('/blogs/{blog}/comments', [CommentController::class, 'store'])->name('blogs.comments.store');
        Route::post('/events/{event}/likes', [LikeController::class, 'store'])->name('events.likes.store');
        Route::post('/blogs/{blog}/likes', [LikeController::class, 'store'])->name('blogs.likes.store');
        Route::get('/events/{event}/views', [ViewController::class, 'increment'])->name('events.views.increment');
        Route::get('/blogs/{blog}/views', [ViewController::class, 'increment'])->name('blogs.views.increment');
    });

    // Routes pour devenir organisateur
    Route::prefix('devenir-organisateur')->name('organizer.request.')->group(function () {
        Route::get('/', [OrganizerRequestController::class, 'create'])->name('create');
        Route::post('/', [OrganizerRequestController::class, 'store'])->name('store');
        Route::get('/status', [OrganizerRequestController::class, 'status'])->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| Routes Organisateur (Redirection vers le préfixe /organizer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'organizer'])->prefix('organisateur')->name('organizer.')->group(function () {
    Route::get('/{any?}', function () {
        return redirect(route('organizer.dashboard'));
    })->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Routes Admin - COMMENTÉ (voir ligne 838 pour le groupe principal)
|--------------------------------------------------------------------------
*/
/*
Route::middleware(['auth', 'verified', 'role:Administrateur'])->prefix('Administrateur')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour les événements
    Route::resource('events', AdminEventController::class);
    
    // Routes spécifiques pour l'approbation/rejet
    Route::post('events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    
    // Route pour l'API des événements en attente
    Route::get('events/pending/api', [AdminEventController::class, 'pendingApi'])->name('events.pending-api');
    Route::get('events/pending', [AdminEventController::class, 'pending'])->name('events.pending');

    // Ressource événements (doit être après les routes spécifiques)
    Route::resource('events', App\Http\Controllers\Admin\AdminEventController::class);

    // Catégories d'événements
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // API routes pour les données du dashboard admin
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/payments', [AdminPaymentController::class, 'getPaymentsData'])->name('payments');
        Route::get('/payments/methods', [AdminDashboardController::class, 'getPaymentMethodsData'])->name('payments.methods');
        Route::get('/payments/trends', [AdminDashboardController::class, 'getTrendsData'])->name('payments.trends');
        Route::get('/events', [AdminEventController::class, 'getEventsData'])->name('events');
        Route::get('/users', [AdminUserController::class, 'getUsersData'])->name('users');
    });

    // Annonces
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');

    // Blogs
    Route::prefix('blogs')->name('blogs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Admin\BlogController::class, 'getData'])->name('data');
        Route::get('/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('store');
        Route::get('/{blog}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('edit');
        Route::put('/{blog}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('update');
        Route::delete('/{blog}', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('destroy');
    });

    // Ajouter cette route pour la compatibilité avec les anciens liens
    Route::get('/blogs', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blogs');

    // Newsletter
    Route::resource('newsletter', NewsletterController::class);

    // Autres routes...
    Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
});
*/

// Redirection pour /admin vers /admin/dashboard
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'role:Administrateur']);

// Routes de paiement (nécessitent un compte vérifié)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/payments/process/{order}', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/process/{order}', [PaymentController::class, 'processPayment'])->name('payments.process.post');
    Route::post('/payments/confirm/{payment}', [PaymentController::class, 'confirmPayment'])->name('payments.confirm');
    Route::get('/payments/waiting/{payment}', [PaymentController::class, 'waiting'])->name('payments.waiting');
    Route::get('/payments/check-status/{payment}', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
    Route::post('/payments/validate-airtel-user', [PaymentController::class, 'validateAirtelUser'])->name('payments.validate-airtel-user');
    Route::get('/payments/success/{payment}', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failure/{payment}', [PaymentController::class, 'failure'])->name('payments.failure');
    Route::get('/payments/cancel/{payment}', [PaymentController::class, 'cancel'])->name('payments.cancel');
});

// Demande Influenceur (web): crée un alias web simple qui met le flag et redirige
Route::post('/influencers/request', function() {
    $user = auth()->user();
    if (!$user) { return redirect()->back()->with('error', 'Veuillez vous connecter.'); }
    if ($user->is_influencer) { return redirect()->back()->with('info', 'Vous êtes déjà influenceur.'); }
    $user->influencer_requested = true;
    $user->save();
    return redirect()->back()->with('success', 'Demande influenceur envoyée.');
})->middleware(['auth', 'verified'])->name('influencers.request');

// Ajoutez ces routes à votre fichier web.php

// Routes pour les tickets (nécessitent un compte vérifié)
Route::middleware(['auth', 'verified'])->group(function () {
    // Route pour régénérer les tickets
    Route::post('/payments/{payment}/regenerate-tickets', [TicketController::class, 'regenerate'])
        ->name('tickets.regenerate');
    
    // Route pour vérifier si les tickets sont prêts
    Route::get('/payments/{payment}/check-tickets', [TicketController::class, 'checkTicketsReady'])
        ->name('tickets.check-ready');
});

// Route webhook (sans middleware auth) - COMMENTÉ: YabetooWebhookController n'existe pas
// Route::post('/webhooks/yabetoo', [YabetooWebhookController::class, 'handleWebhook'])
//    ->name('webhook.yabetoo');

// Route callback Airtel Money (sans middleware auth)
Route::post('/webhooks/airtel/callback', [App\Http\Controllers\AirtelCallbackController::class, 'handleCallback'])
    ->name('webhook.airtel.callback');


// FAQ - Route supprimée car définie plus bas

// Route À propos
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');

// Route Contact
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\PageController::class, 'contactSubmit'])->name('contact.submit');

// Pages légales
Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/cookies', [App\Http\Controllers\PageController::class, 'cookies'])->name('cookies');
Route::get('/faq', [App\Http\Controllers\PageController::class, 'faq'])->name('faq');
Route::get('/help', [App\Http\Controllers\PageController::class, 'help'])->name('help');

// Route de recherche d'événements
Route::get('/events/search', [App\Http\Controllers\EventController::class, 'index'])->name('events.search');

// Routes publiques pour les événements

// Routes des réservations
Route::middleware(['auth'])->prefix('reservations')->name('reservations.')->group(function () {
    Route::post('/', [ReservationController::class, 'store'])->name('store');
    Route::get('/', [ReservationController::class, 'index'])->name('index');
    Route::get('/{reservation}/check-payment-status', [ReservationController::class, 'checkPaymentStatus'])->name('check-payment-status');
    Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
    Route::get('/{reservation}/pay', [ReservationController::class, 'pay'])->name('pay');
    Route::post('/{reservation}/pay', [ReservationController::class, 'processPayment'])->name('processPayment');
    Route::get('/{reservation}/download-tickets', [ReservationController::class, 'downloadTickets'])->name('downloadTickets');
    Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
});

// Vérification email via OTP (doublon aligné)
Route::middleware(['auth'])->group(function () {
    // Routes supprimées - Utilisation de /verification/otp à la place
    // La vérification email se fait maintenant via OTP
});

// Routes pour les demandes d'organisateur
Route::middleware(['auth', 'verified', 'role:Administrateur'])
    ->prefix('admin/organizer-requests')
    ->name('admin.organizer-requests.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\OrganizerRequestController::class, 'index'])->name('index');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\OrganizerRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Admin\OrganizerRequestController::class, 'reject'])->name('reject');
    });
    

// Route pour les utilisateurs normaux pour soumettre une demande
Route::middleware(['auth', 'verified'])
    ->prefix('become-organizer')
    ->name('organizer-requests.')
    ->group(function () {
        Route::get('/', [OrganizerRequestController::class, 'create'])->name('create');
        Route::post('/', [OrganizerRequestController::class, 'store'])->name('store');
        Route::get('/status', [OrganizerRequestController::class, 'status'])->name('status');
    });

// Les routes organisateur sont maintenant dans routes/organizer.php


// Routes ADMIN pour gérer les demandes d'organisateurs
Route::prefix('admin/demandes-organisateurs')->name('admin.organizer-requests.')->middleware(['auth', 'role:Administrateur'])->group(function () {
    Route::get('/', [App\Http\Controllers\OrganizerRequestController::class, 'adminIndex'])->name('index');
    Route::post('/{organizerRequest}/approve', [App\Http\Controllers\OrganizerRequestController::class, 'approve'])->name('approve');
    Route::post('/{organizerRequest}/reject', [App\Http\Controllers\OrganizerRequestController::class, 'reject'])->name('reject');
});

// Les routes PawaPay sont maintenant dans routes/pawapay.php


// Payment routes
Route::middleware('auth')->group(function() {
    Route::get('/payment/process/{order}', [PaymentController::class, 'process'])
        ->name('payment.process');
});


/*
|--------------------------------------------------------------------------
| Routes Authentifiées
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Tableau de bord
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur - Routes déjà définies ligne 1296 - doublons supprimés

    // Gestion des reservations et réservations
    Route::middleware(['auth'])->group(function () {
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    });
});
    // Routes des reservations
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    // Routes de paiement
    Route::prefix('paiements')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments');
        Route::get('/methodes', [App\Http\Controllers\Admin\PaymentController::class, 'methods'])->name('methods');
        Route::get('/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/telecharger', [App\Http\Controllers\Admin\PaymentController::class, 'downloadInvoice'])->name('download');
    });

    // Interactions (commentaires, likes, vues)
    Route::prefix('interactions')->group(function () {
        Route::post('/events/{event}/comments', [App\Http\Controllers\EventCommentController::class, 'store'])->name('events.comments.store');
        Route::post('/blogs/{blog}/comments', [CommentController::class, 'store'])->name('blogs.comments.store');
        Route::post('/events/{event}/likes', [LikeController::class, 'store'])->name('events.likes.store');
        Route::post('/blogs/{blog}/likes', [LikeController::class, 'store'])->name('blogs.likes.store');
        Route::get('/events/{event}/views', [ViewController::class, 'increment'])->name('events.views.increment');
        Route::get('/blogs/{blog}/views', [ViewController::class, 'increment'])->name('blogs.views.increment');
    });

    // Routes pour devenir organisateur
    Route::prefix('devenir-organisateur')->name('organizer.request.')->group(function () {
        Route::get('/', [OrganizerRequestController::class, 'create'])->name('create');
        Route::post('/', [OrganizerRequestController::class, 'store'])->name('store');
        Route::get('/status', [OrganizerRequestController::class, 'status'])->name('status');
    });
});

/*
|--------------------------------------------------------------------------
| Routes Organisateur (Redirection vers le préfixe /organizer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'organizer'])->prefix('organisateur')->name('organizer.')->group(function () {
    Route::get('/{any?}', function () {
        return redirect(route('organizer.dashboard'));
    })->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Routes Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Administrateur'])->prefix('Administrateur')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/activities', [AdminDashboardController::class, 'activities'])->name('activities.index');
    
    // Utilisateurs
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/influencers/approve', [AdminUserController::class, 'approveInfluencer'])->name('users.influencers.approve');
    Route::post('users/{user}/influencers/reject', [AdminUserController::class, 'rejectInfluencer'])->name('users.influencers.reject');
    
    // Tickets
    Route::resource('tickets', AdminTicketController::class);
    
    // Routes pour les promotions de tickets
    Route::get('tickets/{ticket}/promotion', [AdminTicketController::class, 'showPromotionForm'])->name('tickets.promotion-form');
    Route::post('tickets/{ticket}/promotion', [AdminTicketController::class, 'applyPromotion'])->name('tickets.apply-promotion');
    Route::post('tickets/{ticket}/remove-promotion', [AdminTicketController::class, 'removePromotion'])->name('tickets.remove-promotion');
    
    // Paiements
    Route::resource('payments', AdminPaymentController::class);
    Route::get('payments-export', [AdminPaymentController::class, 'export'])->name('payments.export');
    Route::get('payments/{payment}/download', [AdminPaymentController::class, 'download'])->name('payments.download');
    
    // Routes pour les événements
    Route::resource('events', AdminEventController::class);
    
    // Routes spécifiques pour l'approbation/rejet
    Route::post('events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::post('events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
    
    // Routes pour publier/dépublier
    Route::patch('events/{event}/publish', [AdminEventController::class, 'publish'])->name('events.publish');
    Route::patch('events/{event}/unpublish', [AdminEventController::class, 'unpublish'])->name('events.unpublish');
    
    // Route pour l'API des événements en attente
    Route::get('events/pending/api', [AdminEventController::class, 'pendingApi'])->name('events.pending-api');
    Route::get('events/pending', [AdminEventController::class, 'pending'])->name('events.pending');

    // Ressource événements (doit être après les routes spécifiques)
    Route::resource('events', App\Http\Controllers\Admin\AdminEventController::class);

    // Catégories d'événements
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // API routes pour les données du dashboard admin
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/payments', [AdminPaymentController::class, 'getPaymentsData'])->name('payments');
        Route::get('/payments/methods', [AdminDashboardController::class, 'getPaymentMethodsData'])->name('payments.methods');
        Route::get('/payments/trends', [AdminDashboardController::class, 'getTrendsData'])->name('payments.trends');
        Route::get('/events', [AdminEventController::class, 'getEventsData'])->name('events');
        Route::get('/users', [AdminUserController::class, 'getUsersData'])->name('users');
    });

    // Annonces
    Route::resource('announcements', AnnouncementController::class);
    Route::post('announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');

    // Blogs
    Route::resource('blogs', App\Http\Controllers\Admin\BlogController::class);
    Route::get('/blogs/data', [App\Http\Controllers\Admin\BlogController::class, 'getData'])->name('blogs.data');
    
    // Blog Categories
    Route::resource('blog-categories', App\Http\Controllers\Admin\BlogCategoryController::class);

    // Newsletter
    Route::resource('newsletter', NewsletterController::class);

    // Retraits
    Route::get('/withdrawals', [App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/{withdrawal}', [App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::post('/withdrawals/{withdrawal}/approve', [App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Rapports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');

    // Paramètres de commission
    Route::get('/commission-settings', [App\Http\Controllers\Admin\CommissionSettingsController::class, 'index'])->name('commission-settings.index');
    Route::put('/commission-settings', [App\Http\Controllers\Admin\CommissionSettingsController::class, 'update'])->name('commission-settings.update');
    Route::get('/commission-settings/reset', [App\Http\Controllers\Admin\CommissionSettingsController::class, 'reset'])->name('commission-settings.reset');

    // Log Viewer - Visualisation des logs en temps réel
    // Le package opcodesio/log-viewer enregistre automatiquement ses routes
    // Elles seront accessibles via /log-viewer mais protégées par le middleware admin

    // Autres routes...
    Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('stats');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
});

// Redirection pour /admin vers /admin/dashboard
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified', 'role:Administrateur']);


// Route alternative pour events sans le préfixe events
Route::get('/all-events', [App\Http\Controllers\EventController::class, 'index'])->name('all.events');

// Routes des tickets
Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [AdminTicketController::class, 'index'])->name('index');
            Route::get('/create', [AdminTicketController::class, 'create'])->name('create');
            Route::post('/', [AdminTicketController::class, 'store'])->name('store');
            Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
            Route::get('/{ticket}/edit', [AdminTicketController::class, 'edit'])->name('edit');
            Route::put('/{ticket}', [AdminTicketController::class, 'update'])->name('update');
            Route::delete('/{ticket}', [AdminTicketController::class, 'destroy'])->name('destroy');
            
            // Routes pour les promotions
            Route::get('/{ticket}/promotion', [AdminTicketController::class, 'showPromotionForm'])
                ->name('promotion-form');
            Route::post('/{ticket}/promotion', [AdminTicketController::class, 'applyPromotion'])
                ->name('apply-promotion');
            Route::post('/{ticket}/remove-promotion', [AdminTicketController::class, 'removePromotion'])
                ->name('remove-promotion');
        });


    // Routes protégées (nécessitant une authentification) - COMMENTÉ car déjà définies dans le groupe admin principal ligne 833+
    /*
    Route::middleware(['auth', 'verified', 'role:Administrateur|2'])->group(function () {
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/', [EventController::class, 'store'])->name('events.store');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/Administrateur/events/{event}', [AdminEventController::class, 'destroy'])
        ->name('admin.events.destroy');
        Route::post('/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
        
        // Routes spécifiques pour les événements
        Route::get('events/pending', [AdminEventController::class, 'pending'])->name('events.pending');
        Route::get('events/pending-api', [AdminEventController::class, 'pendingApi'])->name('events.pending-api');

        // Ressource événements
        Route::resource('events', AdminEventController::class);
        
        // Routes pour les paiements
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/download', [AdminPaymentController::class, 'download'])->name('payments.download');
    });
    */
// Blog
Route::get('/blogs/articles', [App\Http\Controllers\BlogController::class, 'articles'])->name('blogs.index');
Route::get('/blogs/actualites', [App\Http\Controllers\BlogController::class, 'actualites'])->name('blogs.actualites');
Route::get('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'show'])->name('blogs.show');

// Blog interactions (like et commentaires)
Route::middleware(['auth'])->group(function () {
    Route::post('/blogs/{blog}/like', [App\Http\Controllers\BlogController::class, 'like'])->name('blogs.like');
    Route::post('/blogs/{blog}/comment', [App\Http\Controllers\BlogController::class, 'comment'])->name('blogs.comment');

});

// Gestion des blogs (création/édition) pour Administrateur & Organizer
Route::middleware(['auth', 'role:Administrateur|Organizer'])->group(function () {
    Route::get('/blogs/create', [App\Http\Controllers\BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [App\Http\Controllers\BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'destroy'])->name('blogs.destroy');
});

// Routes pour les clients (utilisateurs normaux)



// Routes pour consulter et télécharger les tickets (nécessitent un compte vérifié)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{order}', [App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/download/{payment}', [App\Http\Controllers\TicketController::class, 'download'])->name('tickets.download');
});


// Route pour la mise à jour du mot de passe
// Route password.update déjà définie ligne 264 avec 'mot-de-passe' - doublon supprimé




// Routes pour l'assistant de création d'événement
Route::middleware(['auth', 'verified', 'role:organizer|Administrateur'])->prefix('events/wizard')->name('events.wizard.')->group(function () {
    // Redirection de l'URL racine vers la première étape
    Route::get('/', function () {
        return redirect()->route('events.wizard.step1');
    });
    
    // Étape 1: Informations de base
    Route::get('/step1', [App\Http\Controllers\EventWizardController::class, 'step1'])
        ->name('step1');
    Route::post('/step1', [App\Http\Controllers\EventWizardController::class, 'postStep1'])
        ->name('post.step1');
        
    // Étape 2: Détails de l'événement
    Route::get('/step2', [App\Http\Controllers\EventWizardController::class, 'step2'])
        ->name('step2');
    Route::post('/step2', [App\Http\Controllers\EventWizardController::class, 'postStep2'])
        ->name('post.step2');
        
    // Étape 3: Billets et tarifs
    Route::get('/step3', [App\Http\Controllers\EventWizardController::class, 'step3'])
        ->name('step3');
    Route::post('/step3', [App\Http\Controllers\EventWizardController::class, 'postStep3'])
        ->name('post.step3');
        
    // Étape 4: Vérification et finalisation
    Route::get('/step4', [App\Http\Controllers\EventWizardController::class, 'step4'])
        ->name('step4');
    Route::post('/step4', [App\Http\Controllers\EventWizardController::class, 'postStep4'])
        ->name('post.step4');
    
    // Aperçu avant finalisation
    Route::get('/preview', [App\Http\Controllers\EventWizardController::class, 'review'])
        ->name('preview');
        
    // Finalisation de la création
    Route::post('/store', [App\Http\Controllers\EventWizardController::class, 'store'])
        ->name('store');
        
    // Page de confirmation
    Route::get('/complete', [App\Http\Controllers\EventWizardController::class, 'complete'])
        ->name('complete');
    Route::post('/complete', [App\Http\Controllers\EventWizardController::class, 'storeComplete'])
        ->name('post.complete');

    // Route pour revenir à une étape spécifique depuis la vue de récapitulation
    Route::get('/step/{step}', [App\Http\Controllers\EventWizardController::class, 'showStep'])
        ->name('step');
});


/*
||--------------------------------------------------------------------------
|| Routes Organisateur
||--------------------------------------------------------------------------
*/

// Routes protégées pour les organisateurs
Route::middleware(['auth', 'verified', 'organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profil organisateur - Utilisation d'un nom différent pour éviter le conflit avec profile.edit
    Route::get('/profile', [OrganizerProfileController::class, 'edit'])->name('organizer.profile.edit');
    Route::put('/profile', [OrganizerProfileController::class, 'update'])->name('organizer.profile.update');

    // Événements
    Route::resource('events', App\Http\Controllers\Organizer\EventController::class)->except(['show']);
    Route::get('events/{event}', [App\Http\Controllers\Organizer\EventController::class, 'show'])->name('events.show');
    Route::post('events/{event}/publish', [App\Http\Controllers\Organizer\EventController::class, 'publish'])->name('events.publish');
    Route::post('events/{event}/unpublish', [App\Http\Controllers\Organizer\EventController::class, 'unpublish'])->name('events.unpublish');
    
    // Billets - définition complète des routes CRUD
    Route::resource('tickets', TicketController::class);
    
    // Scans
    Route::get('scans', [ScanController::class, 'index'])->name('scans.index');
    Route::get('scans/filter', [ScanController::class, 'filter'])->name('scans.filter');
    Route::get('scans/export', [ScanController::class, 'export'])->name('scans.export');
    
    // Paiements
    Route::get('payments', [OrganizerPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [OrganizerPaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/refund', [OrganizerPaymentController::class, 'refund'])->name('payments.refund');
    Route::get('payments/export', [OrganizerPaymentController::class, 'export'])->name('payments.export');
    
    // Codes d'accès
    Route::get('access-codes', [OrganizerDashboardController::class, 'accessCodes'])->name('access-codes.index');
    Route::post('access-codes/generate', [OrganizerDashboardController::class, 'generateAccessCode'])->name('access-codes.generate');
    Route::delete('access-codes/{code}', [OrganizerDashboardController::class, 'deleteAccessCode'])->name('access-codes.delete');
    
    // Debug - Route désactivée (contrôleur n'existe pas)
    // Route::get('debug', [DebugController::class, 'debugData'])->name('debug');
    
    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    
    // Statistiques - rediriger vers analytics
    Route::get('statistics', function() {
        return redirect()->route('organizer.analytics.index');
    })->name('statistics.index');
    
    // Statistiques détaillées - COMMENTÉ: StatsController n'existe pas
    // Route::prefix('stats')->name('stats.')->group(function () {
    //     Route::get('/', [StatsController::class, 'index'])->name('index');
    //     Route::get('/events', [StatsController::class, 'events'])->name('events');
    //     // Route::get('/tickets', [StatsController::class, 'tickets'])->name('tickets');
    // });
    
    // Retraits (Withdrawals)
    Route::get('withdrawals', [App\Http\Controllers\Organizer\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('withdrawals/create', [App\Http\Controllers\Organizer\WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('withdrawals', [App\Http\Controllers\Organizer\WithdrawalController::class, 'store'])->name('withdrawals.store');
});

/*
||--------------------------------------------------------------------------
|| Routes Profil Utilisateur
||--------------------------------------------------------------------------
*/

// Permettre à tous les utilisateurs authentifiés d'accéder au profil
Route::middleware(['auth'])->group(function () {
    // Routes pour le profil utilisateur avec préfixe 'mon-profil'
    Route::prefix('mon-profil')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
        Route::get('/show', [App\Http\Controllers\ProfileController::class, 'edit'])->name('show'); // Alias pour edit
        Route::patch('/', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::get('/mes-billets', [App\Http\Controllers\ProfileController::class, 'tickets'])->name('tickets');
        Route::get('/mes-evenements', [App\Http\Controllers\ProfileController::class, 'events'])->name('events');
    });
});

// Routes de prévisualisation du design des billets (développement)
Route::get('/ticket/design/preview', [App\Http\Controllers\TicketDesignController::class, 'preview'])->name('ticket.design.preview');
Route::get('/ticket/design/pdf', [App\Http\Controllers\TicketDesignController::class, 'downloadPreview'])->name('ticket.design.pdf');

// Routes pour la gestion des casiers de boissons
Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/casiers', [App\Http\Controllers\StockCasiersController::class, 'index'])->name('casiers.index');
    Route::post('/casiers', [App\Http\Controllers\StockCasiersController::class, 'store'])->name('casiers.store');
    Route::get('/casiers/print', [App\Http\Controllers\StockCasiersController::class, 'print'])->name('casiers.print');
    Route::delete('/casiers/{product}', [App\Http\Controllers\StockCasiersController::class, 'destroy'])->name('casiers.destroy');
});

// Routes de débogage (développement)
Route::get('/debug/event-images', [App\Http\Controllers\DebugController::class, 'checkEventImages']);
Route::get('/debug/payment-images/{payment}', [App\Http\Controllers\DebugController::class, 'checkPaymentImages']);
