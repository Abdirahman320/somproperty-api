<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

Route::prefix('v1')->group(function () {

    /* ── PUBLIC (no auth) ── */
    Route::post('auth/owner/login',  [Api\AuthController::class, 'ownerLogin']);
    Route::post('auth/tenant/login', [Api\AuthController::class, 'tenantLogin']);
    Route::post('auth/agent/login',  [Api\AuthController::class, 'agentLogin']);
    Route::post('auth/logout',       [Api\AuthController::class, 'logout']);

    Route::get ('public/listings',            [Api\Public\ListingController::class, 'index']);
    Route::get ('public/listings/{id}',       [Api\Public\ListingController::class, 'show']);
    Route::post('public/listings/{id}/book',  [Api\Public\ListingController::class, 'book']);

    /* ── OWNER ── */
    Route::middleware(['auth:sanctum,owner-api', 'token.role:owner'])->prefix('owner')->group(function () {
        Route::get('dashboard',  [Api\Owner\DashboardController::class, 'index']);
        // Properties
        Route::get   ('properties',            [Api\Owner\PropertyController::class,       'index']);
        Route::post  ('properties',            [Api\Owner\PropertyController::class,       'store']);
        Route::get   ('properties/{id}',       [Api\Owner\PropertyDetailController::class, 'show']);
        Route::put   ('properties/{id}',       [Api\Owner\PropertyDetailController::class, 'update']);
        Route::delete('properties/{id}',       [Api\Owner\PropertyDetailController::class, 'destroy']);
        Route::get   ('properties/{id}/units', [Api\Owner\PropertyController::class,       'units']);
        // Units
        Route::post  ('units',        [Api\Owner\UnitController2::class, 'store']);
        Route::get   ('units/{id}',   [Api\Owner\UnitController2::class, 'show']);
        Route::put   ('units/{id}',   [Api\Owner\UnitController2::class, 'update']);
        Route::delete('units/{id}',   [Api\Owner\UnitController2::class, 'destroy']);
        // Tenants
        Route::get   ('tenants',        [Api\Owner\TenantController::class,       'index']);
        Route::post  ('tenants',        [Api\Owner\TenantController::class,       'store']);
        Route::get   ('tenants/{id}',   [Api\Owner\TenantDetailController::class, 'show']);
        Route::put   ('tenants/{id}',   [Api\Owner\TenantDetailController::class, 'update']);
        Route::delete('tenants/{id}',   [Api\Owner\TenantDetailController::class, 'destroy']);
        // Contracts
        Route::get ('tenants/{id}/contracts',   [Api\Owner\TenantDetailController::class, 'contracts']);
        Route::post('tenants/{id}/contracts',   [Api\Owner\TenantDetailController::class, 'storeContract']);
        Route::put ('contracts/{id}/terminate', [Api\Owner\TenantDetailController::class, 'terminateContract']);
        Route::post('contracts/{id}/renew',     [Api\Owner\TenantDetailController::class, 'renewContract']);
        // Documents
        Route::get   ('documents',                   [Api\Owner\DocumentController::class, 'index']);
        Route::get   ('tenants/{id}/documents',      [Api\Owner\DocumentController::class, 'tenantDocs']);
        Route::post  ('tenants/{id}/documents',      [Api\Owner\DocumentController::class, 'store']);
        Route::get   ('documents/{id}/download',     [Api\Owner\DocumentController::class, 'download']);
        Route::delete('documents/{id}',              [Api\Owner\DocumentController::class, 'destroy']);
        // Billing
        Route::get ('billing',                    [Api\Owner\BillingController::class, 'index']);
        Route::post('billing/generate',           [Api\Owner\BillingController::class, 'generate']);
        Route::post('billing/notify-all',         [Api\Owner\BillingController::class, 'notifyAll']);
        Route::get ('billing/bills/{id}',         [Api\Owner\BillingController::class, 'show']);
        Route::post('billing/bills/{id}/notify',  [Api\Owner\BillingController::class, 'notifySingle']);
        Route::post('billing/bills/{id}/pay',     [Api\Owner\BillingController::class, 'recordPayment']);
        Route::get ('billing/bills/{id}/pdf',     [Api\Owner\BillingController::class, 'pdfApi']);
        Route::post('billing/utility-readings',   [Api\Owner\BillingController::class, 'storeUtilityReading']);
        Route::get ('billing/utility-readings',   [Api\Owner\UtilityController::class,  'index']);
        // Complaints
        Route::get ('complaints',            [Api\Owner\ComplaintController::class, 'index']);
        Route::get ('complaints/{id}',       [Api\Owner\ComplaintController::class, 'show']);
        Route::put ('complaints/{id}',       [Api\Owner\ComplaintController::class, 'update']);
        Route::post('complaints/{id}/reply', [Api\Owner\ComplaintController::class, 'reply']);
        // Assets & issues
        Route::get ('assets',                   [Api\Owner\AssetController::class,  'index']);
        Route::post('assets',                   [Api\Owner\AssetController::class,  'storeAsset']);
        Route::get ('technical-issues',         [Api\Owner\AssetController2::class, 'issues']);
        Route::post('technical-issues',         [Api\Owner\AssetController::class,  'storeIssue']);
        Route::put ('technical-issues/{id}',    [Api\Owner\AssetController2::class, 'updateIssue']);
        // Notifications
        Route::post('notifications/send',   [Api\Owner\NotificationController::class, 'send']);
        Route::get ('notifications/history', [Api\Owner\NotificationController::class, 'history']);
        // Advertisements
        Route::get   ('advertisements',         [Api\Owner\OwnerAdController::class, 'index']);
        Route::post  ('advertisements',         [Api\Owner\OwnerAdController::class, 'store']);
        Route::put   ('advertisements/{id}',    [Api\Owner\OwnerAdController::class, 'update']);
        Route::delete('advertisements/{id}',    [Api\Owner\OwnerAdController::class, 'destroy']);
        Route::get   ('bookings',               [Api\Owner\OwnerAdController::class, 'bookings']);
        Route::put   ('bookings/{id}',          [Api\Owner\OwnerAdController::class, 'updateBooking']);
        // Reports
        Route::get('reports/{type}', [Api\Owner\ReportController::class, 'show'])->where('type', '[a-z_]+');
        // Backup
        Route::get ('backup/export',  [Api\Owner\OwnerBackupController::class, 'export']);
        Route::post('backup/import',  [Api\Owner\OwnerBackupController::class, 'import']);
        // Settings & password
        Route::get ('settings',          [Api\Owner\OwnerSettingsController::class, 'index']);
        Route::put ('settings',          [Api\Owner\OwnerSettingsController::class, 'update']);
        Route::post('profile/password',  [Api\AuthController::class, 'changePasswordOwner']);
    });

    /* ── TENANT ── */
    Route::middleware(['auth:sanctum,tenant-api', 'token.role:tenant'])->prefix('tenant')->group(function () {
        Route::get ('home',                        [Api\Tenant\HomeController::class,         'index']);
        Route::get ('bills',                       [Api\Tenant\BillingController::class,      'index']);
        Route::get ('bills/{id}',                  [Api\Tenant\BillingController::class,      'show']);
        Route::get ('bills/{id}/pdf',              [Api\Tenant\BillingController::class,      'pdf']);
        Route::get ('documents',                   [Api\Tenant\DocumentController::class,     'index']);
        Route::get ('documents/{id}/download',     [Api\Tenant\DocumentController::class,     'download']);
        Route::get ('complaints',                  [Api\Tenant\ComplaintController::class,    'index']);
        Route::post('complaints',                  [Api\Tenant\ComplaintController::class,    'store']);
        Route::get ('complaints/{id}',             [Api\Tenant\ComplaintController::class,    'show']);
        Route::post('complaints/{id}/reply',       [Api\Tenant\ComplaintController::class,    'reply']);
        Route::get ('notifications',               [Api\Tenant\NotificationController::class, 'index']);
        Route::put ('notifications/{id}/read',     [Api\Tenant\NotificationController::class, 'markRead']);
        Route::put ('notifications/mark-all-read', [Api\Tenant\NotificationController::class, 'markAllRead']);
        Route::post('profile/password',            [Api\AuthController::class, 'changePasswordTenant']);
    });

    /* ── AGENT ── */
    Route::middleware(['auth:sanctum,agent-api', 'token.role:agent'])->prefix('agent')->group(function () {
        Route::get ('dashboard',              [Api\Agent\DashboardController::class,     'index']);
        Route::get ('advertisements',         [Api\Agent\AdvertisementController::class, 'index']);
        Route::post('advertisements',         [Api\Agent\AdvertisementController::class, 'store']);
        Route::get ('advertisements/{id}',    [Api\Agent\AdvertisementController::class, 'show']);
        Route::put ('advertisements/{id}',    [Api\Agent\AdvertisementController::class, 'update']);
        Route::delete('advertisements/{id}',  [Api\Agent\AdvertisementController::class, 'destroy']);
        Route::get ('bookings',               [Api\Agent\AdvertisementController::class, 'bookings']);
        Route::put ('bookings/{id}',          [Api\Agent\AdvertisementController::class, 'updateBooking']);
        Route::get ('profile',                [Api\Agent\ProfileController::class,       'index']);
        Route::put ('profile',                [Api\Agent\ProfileController::class,       'update']);
        Route::post('profile/password',       [Api\AuthController::class, 'changePasswordAgent']);
    });
});
