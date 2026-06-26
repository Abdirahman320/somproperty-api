<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Auth, Admin, Owner, Tenant, Agent, PublicHomeController};

/* ── PUBLIC HOME / LISTINGS (no authentication, no payment) ── */
Route::get('/', [PublicHomeController::class, 'index'])->name('home');
Route::get('/listings/{advertisement}', [PublicHomeController::class, 'show'])->name('listings.show');
Route::post('/listings/{advertisement}/book', [PublicHomeController::class, 'book'])->name('listings.book');

/* ── ADMIN ── */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get ('login',  [Auth\AdminAuthController::class,  'showLogin'])->name('login');
    Route::post('login',  [Auth\AdminAuthController::class,  'login']);
    Route::match(['get','post'], 'logout', [Auth\AdminAuthController::class,  'logout'])->name('logout');
    Route::middleware('auth.admin')->group(function () {
        Route::get ('dashboard',     [Admin\DashboardController::class,    'index'])->name('dashboard');
        Route::get ('analytics',     [Admin\AnalyticsController::class,    'index'])->name('analytics');
        Route::get ('revenue',       [Admin\AnalyticsController::class,    'revenue'])->name('revenue');
        Route::get ('subscriptions', [Admin\SubscriptionController::class, 'index'])->name('subscriptions');
        Route::get ('settings',      [Admin\SettingsController::class,     'index'])->name('settings');
        Route::put ('settings',      [Admin\SettingsController::class,     'update'])->name('settings.update');
        Route::get ('audit',         [Admin\AuditController::class,        'index'])->name('audit');
        Route::resource('owners',    Admin\OwnerController::class)->only(['index','create','store','destroy'])->names('owners');
        Route::get ('owners/{owner}/edit',     [Admin\OwnerController::class, 'edit'])->name('owners.edit');
        Route::put ('owners/{owner}',          [Admin\OwnerController::class, 'update'])->name('owners.update');
        Route::put ('owners/{owner}/suspend',  [Admin\OwnerController::class, 'suspend'])->name('owners.suspend');
        Route::put ('owners/{owner}/activate', [Admin\OwnerController::class, 'activate'])->name('owners.activate');
        Route::resource('plans',     Admin\PlanController::class)->only(['index','store','update'])->names('plans');
        // Property Agents (brokers / Dulaal)
        Route::resource('agents',    Admin\AgentController::class)->only(['index','create','store','destroy'])->names('agents');
        Route::put('agents/{agent}/suspend',  [Admin\AgentController::class, 'suspend'])->name('agents.suspend');
        Route::put('agents/{agent}/activate', [Admin\AgentController::class, 'activate'])->name('agents.activate');
        // User Locations
        Route::get('user-locations', [Admin\UserLocationsController::class, 'index'])->name('user-locations');
        // Advertisements
        Route::resource('advertisements', Admin\AdvertisementController::class)->only(['index','create','store','update','destroy'])->names('advertisements');
        // Billing register for advertisements & reports
        Route::get   ('ad-billing',             [Admin\AdBillingController::class, 'index'])->name('ad-billing.index');
        Route::post  ('ad-billing',             [Admin\AdBillingController::class, 'store'])->name('ad-billing.store');
        Route::put   ('ad-billing/{ad_billing}',[Admin\AdBillingController::class, 'update'])->name('ad-billing.update');
        Route::delete('ad-billing/{ad_billing}',[Admin\AdBillingController::class, 'destroy'])->name('ad-billing.destroy');
        // Backup & restore (all tables)
        Route::get ('backup',        [Admin\BackupController::class, 'index'])->name('backup.index');
        Route::get ('backup/export', [Admin\BackupController::class, 'export'])->name('backup.export');
        Route::post('backup/import', [Admin\BackupController::class, 'import'])->name('backup.import');
    });
});

/* ── OWNER ── */
Route::prefix('owner')->name('owner.')->group(function () {
    Route::get ('login',  [Auth\OwnerAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [Auth\OwnerAuthController::class, 'login']);
    Route::match(['get','post'], 'logout', [Auth\OwnerAuthController::class, 'logout'])->name('logout');
    Route::middleware(['auth.owner','active.subscription'])->group(function () {
        Route::get ('dashboard', [Owner\DashboardController::class,     'index'])->name('dashboard');
        Route::get ('reports',   [Owner\ReportController::class,        'index'])->name('reports.index');
        Route::get ('settings',          [Owner\SettingsController::class, 'index'])->name('settings');
        Route::put ('settings',          [Owner\SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/password', [Owner\SettingsController::class, 'changePassword'])->name('settings.password');
        Route::resource('properties', Owner\PropertyController::class)->only(['index','store','update','destroy'])->names('properties');
        Route::resource('units',      Owner\UnitController::class)->only(['create','store','update','destroy'])->names('units');
        Route::resource('tenants',    Owner\TenantController::class)->only(['index','create','store','show','destroy'])->names('tenants');
        Route::post('tenants/{tenant}/contracts',     [Owner\ContractController::class, 'store'])->name('tenants.contracts.store');
        Route::put ('contracts/{contract}/terminate', [Owner\ContractController::class, 'terminate'])->name('contracts.terminate');
        Route::post('contracts/{contract}/renew',     [Owner\ContractController::class, 'renew'])->name('contracts.renew');
        // Tenant documents
        Route::get   ('documents',                           [Owner\TenantDocumentController::class, 'index'])->name('documents.index');
        Route::post  ('tenants/{tenant}/documents',          [Owner\TenantDocumentController::class, 'store'])->name('tenants.documents.store');
        Route::get   ('documents/{document}/download',       [Owner\TenantDocumentController::class, 'download'])->name('documents.download');
        Route::delete('documents/{document}',                [Owner\TenantDocumentController::class, 'destroy'])->name('documents.destroy');
        // Advertisements (multiple images)
        Route::resource('advertisements', Owner\AdvertisementController::class)->only(['index','create','store','update','destroy'])->names('advertisements');
        Route::put('bookings/{booking}', [Owner\AdvertisementController::class, 'updateBooking'])->name('bookings.update');
        // Backup & restore
        Route::get ('backup',        [Owner\BackupController::class, 'index'])->name('backup.index');
        Route::get ('backup/export', [Owner\BackupController::class, 'export'])->name('backup.export');
        Route::post('backup/import', [Owner\BackupController::class, 'import'])->name('backup.import');
        // Billing
        Route::prefix('billing')->name('billing.')->group(function () {
            Route::get ('/',                    [Owner\BillingController::class, 'index'])->name('index');
            Route::post('generate',             [Owner\BillingController::class, 'generate'])->name('generate');
            Route::post('notify-all',           [Owner\BillingController::class, 'notifyAll'])->name('notify-all');
            Route::get ('bills/{bill}',         [Owner\BillingController::class, 'show'])->name('bills.show');
            Route::get ('bills/{bill}/pdf',     [Owner\BillingController::class, 'pdf'])->name('bills.pdf');
            Route::get ('bills/{bill}/pay',     [Owner\BillingController::class, 'showPay'])->name('bills.pay');
            Route::post('bills/{bill}/pay',     [Owner\BillingController::class, 'recordPayment']);
            Route::post('bills/{bill}/notify',  [Owner\BillingController::class, 'notify'])->name('notify');
            Route::get ('utility/create',       [Owner\BillingController::class, 'createUtility'])->name('utility.create');
            Route::post('utility',              [Owner\BillingController::class, 'storeUtility'])->name('utility.store');
        });
        // Notifications
        Route::get ('notifications',       [Owner\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/send',  [Owner\NotificationController::class, 'send'])->name('notifications.send');
        // Assets
        Route::resource('assets',          Owner\AssetController::class)->only(['index','create','store'])->names('assets');
        Route::post('assets/issues',       [Owner\AssetController::class, 'storeIssue'])->name('assets.issues.store');
        Route::get ('assets/issues/create',[Owner\AssetController::class, 'createIssue'])->name('assets.issues.create');
        Route::put ('assets/issues/{issue}',[Owner\AssetController::class,'updateIssue'])->name('assets.issues.update');
        // Complaints
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get ('/',                        [Owner\ComplaintController::class, 'index'])->name('index');
            Route::get ('{complaint}',              [Owner\ComplaintController::class, 'show'])->name('show');
            Route::put ('{complaint}/status',       [Owner\ComplaintController::class, 'updateStatus'])->name('status');
            Route::post('{complaint}/reply',        [Owner\ComplaintController::class, 'reply'])->name('reply');
        });
    });
});

/* ── TENANT ── */
Route::prefix('tenant')->name('tenant.')->group(function () {
    Route::get ('login',  [Auth\TenantAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [Auth\TenantAuthController::class, 'login']);
    Route::match(['get','post'], 'logout', [Auth\TenantAuthController::class, 'logout'])->name('logout');
    Route::middleware('auth.tenant')->group(function () {
        Route::get('home',      [Tenant\HomeController::class, 'index'])->name('home');
        Route::get('documents', [Tenant\HomeController::class, 'documents'])->name('documents');
        Route::get('billing',            [Tenant\BillingController::class, 'index'])->name('billing.index');
        Route::get('billing/{bill}',     [Tenant\BillingController::class, 'show'])->name('billing.show');
        Route::get('billing/{bill}/pdf', [Tenant\BillingController::class, 'pdf'])->name('billing.pdf');
        Route::get ('complaints',            [Tenant\ComplaintController::class, 'index'])->name('complaints.index');
        Route::post('complaints',            [Tenant\ComplaintController::class, 'store'])->name('complaints.store');
        Route::get ('complaints/{complaint}',[Tenant\ComplaintController::class, 'show'])->name('complaints.show');
        Route::post('complaints/{complaint}/reply',[Tenant\ComplaintController::class,'reply'])->name('complaints.reply');
        Route::get ('notifications',         [\App\Http\Controllers\Tenant\NotificationController::class, 'index'])->name('notifications.index');
        Route::put ('notifications/{n}/read',[\App\Http\Controllers\Tenant\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('profile/password',      [Tenant\HomeController::class, 'changePassword'])->name('profile.password');
    });
});

/* ── PROPERTY AGENT (broker / Dulaal) ── */
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get ('login',  [Auth\AgentAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [Auth\AgentAuthController::class, 'login']);
    Route::match(['get','post'], 'logout', [Auth\AgentAuthController::class, 'logout'])->name('logout');
    Route::middleware('auth.agent')->group(function () {
        Route::get ('dashboard',          [Agent\DashboardController::class,     'index'])->name('dashboard');
        Route::get ('profile',            [Agent\ProfileController::class,       'index'])->name('profile');
        Route::put ('profile',            [Agent\ProfileController::class,       'update'])->name('profile.update');
        Route::post('profile/password',   [Agent\ProfileController::class,       'changePassword'])->name('profile.password');
        Route::resource('advertisements', Agent\AdvertisementController::class)
            ->only(['index','create','store','update','destroy'])->names('advertisements');
        Route::put('bookings/{booking}',  [Agent\AdvertisementController::class, 'updateBooking'])->name('bookings.update');
    });
});
