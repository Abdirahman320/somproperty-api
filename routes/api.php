<?php
/**
 * SOM Property Management — REST API
 * All routes backed by MySQL via Laravel/Eloquent.
 * Mobile app (Flutter) connects here. Never directly to MySQL.
 *
 * Base URL: https://yourdomain.com/api/v1
 * Auth:     Laravel Sanctum (Bearer token)
 */
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

Route::prefix('v1')->group(function () {

    /* ── PUBLIC ── */
    Route::post('auth/owner/login',  [Api\AuthController::class, 'ownerLogin']);
    Route::post('auth/tenant/login', [Api\AuthController::class, 'tenantLogin']);
    Route::post('auth/agent/login',  [Api\AuthController::class, 'agentLogin']);

    /* ── AUTHENTICATED ── */
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout',          [Api\AuthController::class, 'logout']);
        Route::post('auth/change-password', [Api\AuthController::class, 'changePassword']);

        /* ════════════════════
           OWNER ENDPOINTS
           (MySQL: owners, properties, units, tenants, contracts,
            tenant_bills, payments, utility_readings, complaints,
            assets, technical_issues, notifications, tenant_notifications)
        ════════════════════ */
        Route::prefix('owner')->group(function () {

            // Dashboard — aggregated stats from MySQL
            Route::get('dashboard', [Api\Owner\DashboardController::class, 'index']);

            // Properties & Units (tables: properties, units)
            Route::get('properties',                    [Api\Owner\PropertyController::class, 'index']);
            Route::post('properties',                   [Api\Owner\PropertyController::class, 'store']);
            Route::get('properties/{id}/units',         [Api\Owner\PropertyController::class, 'units']);

            // Tenants & Contracts (tables: tenants, contracts)
            Route::get('tenants',                       [Api\Owner\TenantController::class, 'index']);
            Route::post('tenants',                      [Api\Owner\TenantController::class, 'store']);

            // Billing (tables: tenant_bills, payments, utility_readings, billing_cycles)
            Route::get('billing',                       [Api\Owner\BillingController::class, 'index']);
            Route::post('billing/generate',             [Api\Owner\BillingController::class, 'generate']);
            Route::post('billing/notify-all',           [Api\Owner\BillingController::class, 'notifyAll']);
            Route::get('billing/bills/{id}',            [Api\Owner\BillingController::class, 'show']);
            Route::post('billing/bills/{id}/notify',    [Api\Owner\BillingController::class, 'notifySingle']);
            Route::post('billing/bills/{id}/pay',       [Api\Owner\BillingController::class, 'recordPayment']);
            Route::post('billing/utility-readings',     [Api\Owner\BillingController::class, 'storeUtilityReading']);

            // Complaints (tables: complaints, complaint_replies)
            Route::get('complaints',                    [Api\Owner\ComplaintController::class, 'index']);
            Route::get('complaints/{id}',               [Api\Owner\ComplaintController::class, 'show']);
            Route::put('complaints/{id}',               [Api\Owner\ComplaintController::class, 'update']);
            Route::post('complaints/{id}/reply',        [Api\Owner\ComplaintController::class, 'reply']);

            // Assets & Issues (tables: assets, technical_issues)
            Route::get('assets',                        [Api\Owner\AssetController::class, 'index']);
            Route::post('assets',                       [Api\Owner\AssetController::class, 'storeAsset']);
            Route::post('technical-issues',             [Api\Owner\AssetController::class, 'storeIssue']);

            // Notifications (tables: notifications, tenant_notifications)
            Route::post('notifications/send',           [Api\Owner\NotificationController::class, 'send']);
            Route::get('notifications/history',         [Api\Owner\NotificationController::class, 'history']);
        });

        /* ════════════════════
           AGENT ENDPOINTS
           (Agents use owner tokens; see available/vacant units across owner properties)
        ════════════════════ */
        Route::prefix('agent')->group(function () {
            Route::get('listings',               [Api\Agent\ListingController::class, 'index']);
            Route::post('listings',              [Api\Agent\ListingController::class, 'store']);
            Route::patch('listings/{id}/status', [Api\Agent\ListingController::class, 'updateStatus']);
            Route::post('bookings',              [Api\Agent\BookingController::class, 'store']);
        });

        /* ════════════════════
           TENANT ENDPOINTS
           (MySQL: tenants, contracts, units, properties,
            tenant_bills, payments, complaints, tenant_notifications)
        ════════════════════ */
        Route::prefix('tenant')->group(function () {

            // Home — contract + unit + property info
            Route::get('home',                          [Api\Tenant\HomeController::class, 'index']);

            // Billing (tables: tenant_bills, payments)
            Route::get('bills',                         [Api\Tenant\BillingController::class, 'index']);
            Route::get('bills/{id}',                    [Api\Tenant\BillingController::class, 'show']);

            // Complaints (tables: complaints, complaint_replies)
            Route::get('complaints',                    [Api\Tenant\ComplaintController::class, 'index']);
            Route::post('complaints',                   [Api\Tenant\ComplaintController::class, 'store']);
            Route::post('complaints/{id}/reply',        [Api\Tenant\ComplaintController::class, 'reply']);

            // Notifications (table: tenant_notifications → notifications)
            Route::get('notifications',                 [Api\Tenant\NotificationController::class, 'index']);
            Route::put('notifications/{id}/read',       [Api\Tenant\NotificationController::class, 'markRead']);
            Route::put('notifications/mark-all-read',   [Api\Tenant\NotificationController::class, 'markAllRead']);
        });
    });
});
