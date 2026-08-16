<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\RidePriceSnapshotController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StripeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\AutoFleet\AutofleetClientWebHookController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\ClientRideController;
use App\Http\Controllers\Client\ClientPaymentMethodController;
use App\Http\Controllers\Client\ClientProfileController;
use App\Http\Controllers\Client\ClientAddressController;
use App\Http\Controllers\AutoFleet\AutoFleetWebHookController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Driver\DriverScheduleController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\DriverSettingsController;
use App\Http\Controllers\Driver\DriverDocumentController;
use App\Http\Controllers\Driver\DriverEarningsController;
use App\Http\Controllers\Driver\DriverLocationController;
use App\Http\Controllers\Driver\DriverRideController;
use App\Http\Controllers\Driver\DriverVehicleController;
use App\Http\Controllers\EnchantController;
use App\Http\Controllers\Enterprise\OrganizationController;
use App\Http\Controllers\Enterprise\OrganizationMemberController;
use App\Http\Controllers\Enterprise\OrganizationBillingController;
use App\Http\Controllers\Enterprise\TenantController;
use App\Http\Controllers\Enterprise\RoleController;
use App\Http\Controllers\Enterprise\PermissionController;
use App\Http\Controllers\Enterprise\ApiKeyController;
use App\Http\Controllers\Enterprise\WebhookSubscriptionController;
use App\Http\Controllers\Fleet\VehicleController;
use App\Http\Controllers\Fleet\VehicleMaintenanceController;
use App\Http\Controllers\Fleet\GarageController;
use App\Http\Controllers\Fleet\FleetAssignmentController;
use App\Http\Controllers\Dispatch\DispatchController;
use App\Http\Controllers\Dispatch\DispatchQueueController;
use App\Http\Controllers\Dispatch\DispatchZoneController;
use App\Http\Controllers\Ride\RideController;
use App\Http\Controllers\Ride\RideEstimateController;
use App\Http\Controllers\Ride\RideStatusController;
use App\Http\Controllers\Ride\RideFeedbackController;
use App\Http\Controllers\Ride\RideCancellationController;
use App\Http\Controllers\Pricing\FareController;
use App\Http\Controllers\Pricing\SurgeController;
use App\Http\Controllers\Pricing\PromoCodeController;
use App\Http\Controllers\Pricing\ZonePricingController;
use App\Http\Controllers\Billing\InvoiceController;
use App\Http\Controllers\Billing\PaymentController;
use App\Http\Controllers\Billing\RefundController;
use App\Http\Controllers\Billing\WalletController;
use App\Http\Controllers\Billing\SettlementController;
use App\Http\Controllers\Reporting\AnalyticsController;
use App\Http\Controllers\Reporting\ExportController;
use App\Http\Controllers\Reporting\KpiController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationPreferenceController;
use App\Http\Controllers\Notification\PushDeviceController;
use App\Http\Controllers\Support\TicketController;
use App\Http\Controllers\Support\TicketMessageController;
use App\Http\Controllers\Support\KnowledgeBaseController;
use App\Http\Controllers\Compliance\DocumentController;
use App\Http\Controllers\Compliance\BackgroundCheckController;
use App\Http\Controllers\Compliance\InsuranceController;
use App\Http\Controllers\Geo\GeofenceController;
use App\Http\Controllers\Geo\CityController;
use App\Http\Controllers\Geo\AirportController;
use App\Http\Controllers\Partner\PartnerController;
use App\Http\Controllers\Partner\PartnerContractController;
use App\Http\Controllers\Marketplace\CatalogController;
use App\Http\Controllers\Marketplace\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:global'])->group(function () {

    // AUTHENTICATION Routes
    Route::prefix('auth')->group(function () {
        Route::prefix('driver')->group(function () {
            Route::prefix('sms')->group(function () {
                Route::post('login', [DriverAuthController::class, 'login'])->middleware(['throttle:sms']);
                Route::post('verify', [DriverAuthController::class, 'verify'])->middleware(['throttle:sms']);
            });

            Route::post('logout', [DriverAuthController::class, 'logout'])->middleware(['auth:sanctum', 'abilities:driver.portal']);
            Route::post('refresh', [DriverAuthController::class, 'refresh'])->middleware(['auth:sanctum', 'abilities:driver.portal']);
            Route::get('session', [DriverAuthController::class, 'session'])->middleware(['auth:sanctum', 'abilities:driver.portal']);
        });

        Route::prefix('client')->group(function () {
            Route::post('register', [RegisteredUserController::class, 'store'])->middleware(['throttle:auth']);
            Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:auth']);
            Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'abilities:client.portal']);
            Route::post('refresh', [AuthController::class, 'refresh'])->middleware(['auth:sanctum', 'abilities:client.portal']);
            Route::get('me', [AuthController::class, 'me'])->middleware(['auth:sanctum', 'abilities:client.portal']);
            Route::post('password/forgot', [PasswordResetController::class, 'forgot'])->middleware(['throttle:auth']);
            Route::post('password/reset', [PasswordResetController::class, 'reset'])->middleware(['throttle:auth']);
            Route::post('password/change', [PasswordResetController::class, 'change'])->middleware(['auth:sanctum', 'abilities:client.portal']);
        });

        Route::prefix('admin')->group(function () {
            Route::post('login', [AuthController::class, 'adminLogin'])->middleware(['throttle:auth']);
            Route::post('logout', [AuthController::class, 'adminLogout'])->middleware(['auth:sanctum', 'abilities:admin.portal']);
            Route::get('me', [AuthController::class, 'adminMe'])->middleware(['auth:sanctum', 'abilities:admin.portal']);
        });

        Route::prefix('oauth')->group(function () {
            Route::get('{provider}/redirect', [OAuthController::class, 'redirect']);
            Route::get('{provider}/callback', [OAuthController::class, 'callback']);
            Route::post('{provider}/token', [OAuthController::class, 'token']);
            Route::post('revoke', [OAuthController::class, 'revoke'])->middleware(['auth:sanctum']);
        });

        Route::prefix('2fa')->middleware(['auth:sanctum'])->group(function () {
            Route::post('enable', [TwoFactorController::class, 'enable']);
            Route::post('confirm', [TwoFactorController::class, 'confirm']);
            Route::post('disable', [TwoFactorController::class, 'disable']);
            Route::post('verify', [TwoFactorController::class, 'verify']);
            Route::get('recovery-codes', [TwoFactorController::class, 'recoveryCodes']);
            Route::post('recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes']);
        });
    });

    // DRIVER Routes
    Route::prefix('driver')
        ->middleware(['auth:sanctum', 'abilities:driver.portal'])
        ->group(function () {
            Route::get('/settings', [DriverSettingsController::class, 'index']);
            Route::get('/settings/{key}', [DriverSettingsController::class, 'show']);
            Route::put('/settings/{key}', [DriverSettingsController::class, 'update']);
            Route::patch('/settings', [DriverSettingsController::class, 'bulkUpdate']);

            Route::get('/earnings', [DriverEarningsController::class, 'index']);
            Route::get('/earnings/summary', [DriverEarningsController::class, 'summary']);
            Route::get('/earnings/statements', [DriverEarningsController::class, 'statements']);
            Route::get('/earnings/statements/{statement}', [DriverEarningsController::class, 'showStatement']);
            Route::get('/earnings/payouts', [DriverEarningsController::class, 'payouts']);

            Route::post('/location', [DriverLocationController::class, 'update']);
            Route::get('/location/history', [DriverLocationController::class, 'history']);
            Route::post('/location/heartbeat', [DriverLocationController::class, 'heartbeat']);

            Route::get('/rides', [DriverRideController::class, 'index']);
            Route::get('/rides/active', [DriverRideController::class, 'active']);
            Route::get('/rides/{ride}', [DriverRideController::class, 'show']);
            Route::post('/rides/{ride}/accept', [DriverRideController::class, 'accept']);
            Route::post('/rides/{ride}/decline', [DriverRideController::class, 'decline']);
            Route::post('/rides/{ride}/arrive', [DriverRideController::class, 'arrive']);
            Route::post('/rides/{ride}/start', [DriverRideController::class, 'start']);
            Route::post('/rides/{ride}/complete', [DriverRideController::class, 'complete']);
            Route::post('/rides/{ride}/cancel', [DriverRideController::class, 'cancel']);

            Route::get('/vehicles', [DriverVehicleController::class, 'index']);
            Route::post('/vehicles/{vehicle}/select', [DriverVehicleController::class, 'select']);
            Route::get('/vehicles/{vehicle}', [DriverVehicleController::class, 'show']);

            Route::get('/documents', [DriverDocumentController::class, 'index']);
            Route::post('/documents', [DriverDocumentController::class, 'store']);
            Route::get('/documents/{document}', [DriverDocumentController::class, 'show']);
            Route::delete('/documents/{document}', [DriverDocumentController::class, 'destroy']);

            Route::prefix('{driver}')
                ->group(function () {
                    Route::get('/', [DriverController::class, 'get']);
                    Route::put('/', [DriverController::class, 'update']);
                    Route::patch('/status', [DriverController::class, 'updateStatus']);
                    Route::get('/profile', [DriverController::class, 'profile']);
                    Route::put('/profile', [DriverController::class, 'updateProfile']);
                    Route::post('/online', [DriverController::class, 'goOnline']);
                    Route::post('/offline', [DriverController::class, 'goOffline']);

                    Route::prefix('schedule')->group(function () {
                        Route::get('/weekly', [DriverScheduleController::class, 'weekly']);
                        Route::get('/city', [DriverScheduleController::class, 'city']);
                        Route::get('/', [DriverScheduleController::class, 'index']);
                        Route::post('/', [DriverScheduleController::class, 'store']);
                        Route::put('/{schedule}', [DriverScheduleController::class, 'update']);
                        Route::delete('/{schedule}', [DriverScheduleController::class, 'delete']);
                        Route::post('/bulk', [DriverScheduleController::class, 'bulkStore']);
                    });
                })
                ->scopeBindings();
        });

    // CLIENT Routes
    Route::prefix('client')
        ->middleware(['auth:sanctum', 'abilities:client.portal'])
        ->group(function () {
            Route::get('/profile', [ClientProfileController::class, 'show']);
            Route::put('/profile', [ClientProfileController::class, 'update']);
            Route::delete('/profile', [ClientProfileController::class, 'destroy']);
            Route::post('/profile/avatar', [ClientProfileController::class, 'uploadAvatar']);

            Route::get('/addresses', [ClientAddressController::class, 'index']);
            Route::post('/addresses', [ClientAddressController::class, 'store']);
            Route::put('/addresses/{address}', [ClientAddressController::class, 'update']);
            Route::delete('/addresses/{address}', [ClientAddressController::class, 'destroy']);
            Route::post('/addresses/{address}/default', [ClientAddressController::class, 'setDefault']);

            Route::get('/payment-methods', [ClientPaymentMethodController::class, 'index']);
            Route::post('/payment-methods', [ClientPaymentMethodController::class, 'store']);
            Route::delete('/payment-methods/{paymentMethod}', [ClientPaymentMethodController::class, 'destroy']);
            Route::post('/payment-methods/{paymentMethod}/default', [ClientPaymentMethodController::class, 'setDefault']);

            Route::get('/rides', [ClientRideController::class, 'index']);
            Route::get('/rides/{ride}', [ClientRideController::class, 'show']);
            Route::post('/rides', [ClientRideController::class, 'store']);
            Route::post('/rides/{ride}/cancel', [ClientRideController::class, 'cancel']);
            Route::get('/rides/{ride}/receipt', [ClientRideController::class, 'receipt']);
            Route::post('/rides/{ride}/rate', [ClientRideController::class, 'rate']);
            Route::post('/rides/{ride}/tip', [ClientRideController::class, 'tip']);
        });

    // RIDE / BOOKING Routes
    Route::prefix('rides')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [RideController::class, 'index']);
        Route::post('/', [RideController::class, 'store']);
        Route::get('/{ride}', [RideController::class, 'show']);
        Route::put('/{ride}', [RideController::class, 'update']);
        Route::delete('/{ride}', [RideController::class, 'destroy']);
        Route::get('/{ride}/timeline', [RideController::class, 'timeline']);
        Route::get('/{ride}/tracking', [RideController::class, 'tracking']);
        Route::get('/{ride}/route', [RideController::class, 'route']);
        Route::post('/{ride}/reassign', [RideController::class, 'reassign']);
        Route::post('/{ride}/notes', [RideController::class, 'addNote']);

        Route::post('/estimate', [RideEstimateController::class, 'estimate']);
        Route::post('/estimate/compare', [RideEstimateController::class, 'compare']);
        Route::get('/estimate/{estimate}', [RideEstimateController::class, 'show']);

        Route::get('/{ride}/status', [RideStatusController::class, 'show']);
        Route::patch('/{ride}/status', [RideStatusController::class, 'update']);
        Route::get('/{ride}/status/history', [RideStatusController::class, 'history']);

        Route::post('/{ride}/feedback', [RideFeedbackController::class, 'store']);
        Route::get('/{ride}/feedback', [RideFeedbackController::class, 'show']);

        Route::post('/{ride}/cancel', [RideCancellationController::class, 'cancel']);
        Route::get('/cancellation-reasons', [RideCancellationController::class, 'reasons']);
        Route::get('/{ride}/cancellation', [RideCancellationController::class, 'show']);
    });

    // DISPATCH Routes
    Route::prefix('dispatch')->middleware(['auth:sanctum', 'abilities:dispatch.portal'])->group(function () {
        Route::get('/board', [DispatchController::class, 'board']);
        Route::get('/available-drivers', [DispatchController::class, 'availableDrivers']);
        Route::post('/assign', [DispatchController::class, 'assign']);
        Route::post('/unassign', [DispatchController::class, 'unassign']);
        Route::post('/manual-offer', [DispatchController::class, 'manualOffer']);
        Route::get('/heatmap', [DispatchController::class, 'heatmap']);

        Route::get('/queues', [DispatchQueueController::class, 'index']);
        Route::post('/queues', [DispatchQueueController::class, 'store']);
        Route::get('/queues/{queue}', [DispatchQueueController::class, 'show']);
        Route::put('/queues/{queue}', [DispatchQueueController::class, 'update']);
        Route::delete('/queues/{queue}', [DispatchQueueController::class, 'destroy']);
        Route::post('/queues/{queue}/reorder', [DispatchQueueController::class, 'reorder']);

        Route::get('/zones', [DispatchZoneController::class, 'index']);
        Route::post('/zones', [DispatchZoneController::class, 'store']);
        Route::get('/zones/{zone}', [DispatchZoneController::class, 'show']);
        Route::put('/zones/{zone}', [DispatchZoneController::class, 'update']);
        Route::delete('/zones/{zone}', [DispatchZoneController::class, 'destroy']);
        Route::get('/zones/{zone}/capacity', [DispatchZoneController::class, 'capacity']);
    });

    // FLEET / VEHICLE Routes
    Route::prefix('fleet')->middleware(['auth:sanctum', 'abilities:fleet.portal'])->group(function () {
        Route::get('/vehicles', [VehicleController::class, 'index']);
        Route::post('/vehicles', [VehicleController::class, 'store']);
        Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update']);
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);
        Route::patch('/vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus']);
        Route::get('/vehicles/{vehicle}/telemetry', [VehicleController::class, 'telemetry']);
        Route::get('/vehicles/{vehicle}/trips', [VehicleController::class, 'trips']);

        Route::get('/vehicles/{vehicle}/maintenance', [VehicleMaintenanceController::class, 'index']);
        Route::post('/vehicles/{vehicle}/maintenance', [VehicleMaintenanceController::class, 'store']);
        Route::get('/maintenance/{maintenance}', [VehicleMaintenanceController::class, 'show']);
        Route::put('/maintenance/{maintenance}', [VehicleMaintenanceController::class, 'update']);
        Route::post('/maintenance/{maintenance}/complete', [VehicleMaintenanceController::class, 'complete']);

        Route::get('/garages', [GarageController::class, 'index']);
        Route::post('/garages', [GarageController::class, 'store']);
        Route::get('/garages/{garage}', [GarageController::class, 'show']);
        Route::put('/garages/{garage}', [GarageController::class, 'update']);
        Route::delete('/garages/{garage}', [GarageController::class, 'destroy']);
        Route::get('/garages/{garage}/inventory', [GarageController::class, 'inventory']);

        Route::get('/assignments', [FleetAssignmentController::class, 'index']);
        Route::post('/assignments', [FleetAssignmentController::class, 'store']);
        Route::delete('/assignments/{assignment}', [FleetAssignmentController::class, 'destroy']);
        Route::post('/assignments/{assignment}/transfer', [FleetAssignmentController::class, 'transfer']);
    });

    // PRICING Routes
    Route::prefix('pricing')->middleware(['auth:sanctum'])->group(function () {
        Route::post('/fare/calculate', [FareController::class, 'calculate']);
        Route::get('/fare/rules', [FareController::class, 'rules']);
        Route::get('/fare/rules/{rule}', [FareController::class, 'showRule']);
        Route::post('/fare/rules', [FareController::class, 'storeRule'])->middleware(['abilities:admin.portal']);
        Route::put('/fare/rules/{rule}', [FareController::class, 'updateRule'])->middleware(['abilities:admin.portal']);
        Route::delete('/fare/rules/{rule}', [FareController::class, 'destroyRule'])->middleware(['abilities:admin.portal']);

        Route::get('/surge', [SurgeController::class, 'index']);
        Route::get('/surge/active', [SurgeController::class, 'active']);
        Route::post('/surge', [SurgeController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::put('/surge/{surge}', [SurgeController::class, 'update'])->middleware(['abilities:admin.portal']);
        Route::delete('/surge/{surge}', [SurgeController::class, 'destroy'])->middleware(['abilities:admin.portal']);

        Route::get('/promo-codes', [PromoCodeController::class, 'index']);
        Route::post('/promo-codes/validate', [PromoCodeController::class, 'validate']);
        Route::post('/promo-codes', [PromoCodeController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::put('/promo-codes/{promoCode}', [PromoCodeController::class, 'update'])->middleware(['abilities:admin.portal']);
        Route::delete('/promo-codes/{promoCode}', [PromoCodeController::class, 'destroy'])->middleware(['abilities:admin.portal']);
        Route::get('/promo-codes/{promoCode}/redemptions', [PromoCodeController::class, 'redemptions'])->middleware(['abilities:admin.portal']);

        Route::get('/zones', [ZonePricingController::class, 'index']);
        Route::post('/zones', [ZonePricingController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::get('/zones/{zone}', [ZonePricingController::class, 'show']);
        Route::put('/zones/{zone}', [ZonePricingController::class, 'update'])->middleware(['abilities:admin.portal']);
        Route::delete('/zones/{zone}', [ZonePricingController::class, 'destroy'])->middleware(['abilities:admin.portal']);
    });

    // BILLING / PAYMENTS Routes
    Route::prefix('billing')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::post('/invoices', [InvoiceController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->middleware(['abilities:admin.portal']);
        Route::post('/invoices/{invoice}/void', [InvoiceController::class, 'void'])->middleware(['abilities:admin.portal']);
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf']);
        Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'pay']);

        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
        Route::post('/payments/{payment}/capture', [PaymentController::class, 'capture']);
        Route::post('/payments/{payment}/confirm', [PaymentController::class, 'confirm']);

        Route::get('/refunds', [RefundController::class, 'index']);
        Route::post('/refunds', [RefundController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::get('/refunds/{refund}', [RefundController::class, 'show']);
        Route::post('/refunds/{refund}/approve', [RefundController::class, 'approve'])->middleware(['abilities:admin.portal']);
        Route::post('/refunds/{refund}/reject', [RefundController::class, 'reject'])->middleware(['abilities:admin.portal']);

        Route::get('/wallet', [WalletController::class, 'show']);
        Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
        Route::post('/wallet/top-up', [WalletController::class, 'topUp']);
        Route::post('/wallet/transfer', [WalletController::class, 'transfer']);

        Route::get('/settlements', [SettlementController::class, 'index'])->middleware(['abilities:admin.portal']);
        Route::post('/settlements/run', [SettlementController::class, 'run'])->middleware(['abilities:admin.portal']);
        Route::get('/settlements/{settlement}', [SettlementController::class, 'show'])->middleware(['abilities:admin.portal']);
        Route::post('/settlements/{settlement}/approve', [SettlementController::class, 'approve'])->middleware(['abilities:admin.portal']);
    });

    // ENTERPRISE / ORGANIZATION Routes
    Route::prefix('enterprise')->middleware(['auth:sanctum', 'abilities:enterprise.portal'])->group(function () {
        Route::get('/organizations', [OrganizationController::class, 'index']);
        Route::post('/organizations', [OrganizationController::class, 'store']);
        Route::get('/organizations/{organization}', [OrganizationController::class, 'show']);
        Route::put('/organizations/{organization}', [OrganizationController::class, 'update']);
        Route::delete('/organizations/{organization}', [OrganizationController::class, 'destroy']);
        Route::get('/organizations/{organization}/usage', [OrganizationController::class, 'usage']);
        Route::get('/organizations/{organization}/settings', [OrganizationController::class, 'settings']);
        Route::put('/organizations/{organization}/settings', [OrganizationController::class, 'updateSettings']);

        Route::get('/organizations/{organization}/members', [OrganizationMemberController::class, 'index']);
        Route::post('/organizations/{organization}/members', [OrganizationMemberController::class, 'store']);
        Route::put('/organizations/{organization}/members/{member}', [OrganizationMemberController::class, 'update']);
        Route::delete('/organizations/{organization}/members/{member}', [OrganizationMemberController::class, 'destroy']);
        Route::post('/organizations/{organization}/members/invite', [OrganizationMemberController::class, 'invite']);
        Route::post('/organizations/{organization}/members/{member}/resend-invite', [OrganizationMemberController::class, 'resendInvite']);

        Route::get('/organizations/{organization}/billing', [OrganizationBillingController::class, 'show']);
        Route::put('/organizations/{organization}/billing', [OrganizationBillingController::class, 'update']);
        Route::get('/organizations/{organization}/billing/invoices', [OrganizationBillingController::class, 'invoices']);
        Route::post('/organizations/{organization}/billing/payment-method', [OrganizationBillingController::class, 'attachPaymentMethod']);

        Route::get('/tenants', [TenantController::class, 'index']);
        Route::post('/tenants', [TenantController::class, 'store']);
        Route::get('/tenants/{tenant}', [TenantController::class, 'show']);
        Route::put('/tenants/{tenant}', [TenantController::class, 'update']);
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy']);
        Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend']);
        Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate']);

        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::put('/roles/{role}/permissions', [RoleController::class, 'syncPermissions']);

        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::get('/permissions/matrix', [PermissionController::class, 'matrix']);

        Route::get('/api-keys', [ApiKeyController::class, 'index']);
        Route::post('/api-keys', [ApiKeyController::class, 'store']);
        Route::get('/api-keys/{apiKey}', [ApiKeyController::class, 'show']);
        Route::put('/api-keys/{apiKey}', [ApiKeyController::class, 'update']);
        Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy']);
        Route::post('/api-keys/{apiKey}/rotate', [ApiKeyController::class, 'rotate']);
        Route::post('/api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke']);

        Route::get('/webhook-subscriptions', [WebhookSubscriptionController::class, 'index']);
        Route::post('/webhook-subscriptions', [WebhookSubscriptionController::class, 'store']);
        Route::get('/webhook-subscriptions/{subscription}', [WebhookSubscriptionController::class, 'show']);
        Route::put('/webhook-subscriptions/{subscription}', [WebhookSubscriptionController::class, 'update']);
        Route::delete('/webhook-subscriptions/{subscription}', [WebhookSubscriptionController::class, 'destroy']);
        Route::post('/webhook-subscriptions/{subscription}/test', [WebhookSubscriptionController::class, 'test']);
        Route::get('/webhook-subscriptions/{subscription}/deliveries', [WebhookSubscriptionController::class, 'deliveries']);
    });

    // REPORTING / ANALYTICS Routes
    Route::prefix('reporting')->middleware(['auth:sanctum', 'abilities:reporting.portal'])->group(function () {
        Route::get('/analytics/overview', [AnalyticsController::class, 'overview']);
        Route::get('/analytics/rides', [AnalyticsController::class, 'rides']);
        Route::get('/analytics/revenue', [AnalyticsController::class, 'revenue']);
        Route::get('/analytics/drivers', [AnalyticsController::class, 'drivers']);
        Route::get('/analytics/clients', [AnalyticsController::class, 'clients']);
        Route::get('/analytics/funnel', [AnalyticsController::class, 'funnel']);
        Route::get('/analytics/retention', [AnalyticsController::class, 'retention']);

        Route::get('/kpis', [KpiController::class, 'index']);
        Route::get('/kpis/{kpi}', [KpiController::class, 'show']);
        Route::get('/kpis/{kpi}/series', [KpiController::class, 'series']);

        Route::post('/exports', [ExportController::class, 'store']);
        Route::get('/exports', [ExportController::class, 'index']);
        Route::get('/exports/{export}', [ExportController::class, 'show']);
        Route::get('/exports/{export}/download', [ExportController::class, 'download']);
        Route::delete('/exports/{export}', [ExportController::class, 'destroy']);
    });

    // NOTIFICATIONS Routes
    Route::prefix('notifications')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);

        Route::get('/preferences', [NotificationPreferenceController::class, 'index']);
        Route::put('/preferences', [NotificationPreferenceController::class, 'update']);

        Route::get('/devices', [PushDeviceController::class, 'index']);
        Route::post('/devices', [PushDeviceController::class, 'store']);
        Route::delete('/devices/{device}', [PushDeviceController::class, 'destroy']);
    });

    // SUPPORT / TICKETS Routes
    Route::prefix('support')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
        Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
        Route::post('/tickets/{ticket}/close', [TicketController::class, 'close']);
        Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'reopen']);
        Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->middleware(['abilities:admin.portal']);

        Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index']);
        Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store']);

        Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index']);
        Route::get('/knowledge-base/{article}', [KnowledgeBaseController::class, 'show']);
        Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::put('/knowledge-base/{article}', [KnowledgeBaseController::class, 'update'])->middleware(['abilities:admin.portal']);
        Route::delete('/knowledge-base/{article}', [KnowledgeBaseController::class, 'destroy'])->middleware(['abilities:admin.portal']);
    });

    // COMPLIANCE Routes
    Route::prefix('compliance')->middleware(['auth:sanctum', 'abilities:compliance.portal'])->group(function () {
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::post('/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
        Route::post('/documents/{document}/approve', [DocumentController::class, 'approve']);
        Route::post('/documents/{document}/reject', [DocumentController::class, 'reject']);
        Route::get('/documents/expiring', [DocumentController::class, 'expiring']);

        Route::get('/background-checks', [BackgroundCheckController::class, 'index']);
        Route::post('/background-checks', [BackgroundCheckController::class, 'store']);
        Route::get('/background-checks/{check}', [BackgroundCheckController::class, 'show']);
        Route::post('/background-checks/{check}/refresh', [BackgroundCheckController::class, 'refresh']);

        Route::get('/insurance', [InsuranceController::class, 'index']);
        Route::post('/insurance', [InsuranceController::class, 'store']);
        Route::get('/insurance/{policy}', [InsuranceController::class, 'show']);
        Route::put('/insurance/{policy}', [InsuranceController::class, 'update']);
        Route::delete('/insurance/{policy}', [InsuranceController::class, 'destroy']);
    });

    // GEO / LOCATION INTEL Routes
    Route::prefix('geo')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/geofences', [GeofenceController::class, 'index']);
        Route::post('/geofences', [GeofenceController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::get('/geofences/{geofence}', [GeofenceController::class, 'show']);
        Route::put('/geofences/{geofence}', [GeofenceController::class, 'update'])->middleware(['abilities:admin.portal']);
        Route::delete('/geofences/{geofence}', [GeofenceController::class, 'destroy'])->middleware(['abilities:admin.portal']);
        Route::post('/geofences/contains', [GeofenceController::class, 'contains']);

        Route::get('/cities', [CityController::class, 'index']);
        Route::get('/cities/{city}', [CityController::class, 'show']);
        Route::get('/cities/{city}/services', [CityController::class, 'services']);
        Route::put('/cities/{city}', [CityController::class, 'update'])->middleware(['abilities:admin.portal']);

        Route::get('/airports', [AirportController::class, 'index']);
        Route::get('/airports/{airport}', [AirportController::class, 'show']);
        Route::get('/airports/{airport}/pickup-zones', [AirportController::class, 'pickupZones']);
    });

    // PARTNER Routes
    Route::prefix('partners')->middleware(['auth:sanctum', 'abilities:partner.portal'])->group(function () {
        Route::get('/', [PartnerController::class, 'index']);
        Route::post('/', [PartnerController::class, 'store']);
        Route::get('/{partner}', [PartnerController::class, 'show']);
        Route::put('/{partner}', [PartnerController::class, 'update']);
        Route::delete('/{partner}', [PartnerController::class, 'destroy']);
        Route::get('/{partner}/performance', [PartnerController::class, 'performance']);

        Route::get('/{partner}/contracts', [PartnerContractController::class, 'index']);
        Route::post('/{partner}/contracts', [PartnerContractController::class, 'store']);
        Route::get('/{partner}/contracts/{contract}', [PartnerContractController::class, 'show']);
        Route::put('/{partner}/contracts/{contract}', [PartnerContractController::class, 'update']);
        Route::post('/{partner}/contracts/{contract}/activate', [PartnerContractController::class, 'activate']);
        Route::post('/{partner}/contracts/{contract}/terminate', [PartnerContractController::class, 'terminate']);
    });

    // MARKETPLACE Routes
    Route::prefix('marketplace')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/catalog', [CatalogController::class, 'index']);
        Route::get('/catalog/{item}', [CatalogController::class, 'show']);
        Route::post('/catalog', [CatalogController::class, 'store'])->middleware(['abilities:admin.portal']);
        Route::put('/catalog/{item}', [CatalogController::class, 'update'])->middleware(['abilities:admin.portal']);
        Route::delete('/catalog/{item}', [CatalogController::class, 'destroy'])->middleware(['abilities:admin.portal']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
        Route::get('/orders/{order}/status', [OrderController::class, 'status']);
    });

    // ADMIN Routes
    Route::prefix('admin')->middleware('admin-key')->group(function () {
        Route::post('client/find', [ClientController::class, 'search']);
        Route::get('client/{id}/rides', [ClientController::class, 'ridesById']);
        Route::get('client/{id}', [ClientController::class, 'show']);
        Route::put('client/{id}', [ClientController::class, 'update']);
        Route::post('client/{id}/suspend', [ClientController::class, 'suspend']);
        Route::post('client/{id}/activate', [ClientController::class, 'activate']);

        Route::get('services/{id}', [ServiceController::class, 'show']);
        Route::get('services', [ServiceController::class, 'index']);
        Route::post('services', [ServiceController::class, 'store']);
        Route::put('services/{id}', [ServiceController::class, 'update']);
        Route::delete('services/{id}', [ServiceController::class, 'destroy']);

        Route::get('stripe/payment-intent/{paymentId}', [StripeController::class, 'paymentIntent']);
        Route::get('stripe/charge/{chargeId}', [StripeController::class, 'charge']);
        Route::get('stripe/customers/{customerId}', [StripeController::class, 'customer']);
        Route::get('stripe/refunds/{refundId}', [StripeController::class, 'refund']);
        Route::post('stripe/refunds', [StripeController::class, 'createRefund']);

        Route::get('ride-price-snapshots', [RidePriceSnapshotController::class, 'index']);
        Route::get('ride-price-snapshots/{snapshot}', [RidePriceSnapshotController::class, 'show']);

        Route::get('dashboard/overview', [DashboardController::class, 'overview']);
        Route::get('dashboard/live', [DashboardController::class, 'live']);
        Route::get('dashboard/alerts', [DashboardController::class, 'alerts']);

        Route::get('audit-logs', [AuditLogController::class, 'index']);
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show']);
        Route::get('audit-logs/export', [AuditLogController::class, 'export']);

        Route::get('feature-flags', [FeatureFlagController::class, 'index']);
        Route::post('feature-flags', [FeatureFlagController::class, 'store']);
        Route::put('feature-flags/{flag}', [FeatureFlagController::class, 'update']);
        Route::delete('feature-flags/{flag}', [FeatureFlagController::class, 'destroy']);
        Route::post('feature-flags/{flag}/toggle', [FeatureFlagController::class, 'toggle']);

        Route::get('config', [ConfigController::class, 'index']);
        Route::get('config/{key}', [ConfigController::class, 'show']);
        Route::put('config/{key}', [ConfigController::class, 'update']);
        Route::post('config/bulk', [ConfigController::class, 'bulkUpdate']);
    });
});

Route::prefix('enchant')->group(function () {
    Route::post('/customer', [EnchantController::class, 'customerView']);
    Route::post('/ticket', [EnchantController::class, 'ticketView']);
    Route::post('/order', [EnchantController::class, 'orderView']);
});

Route::prefix('webhook')->group(function () {
    Route::post('/ride-updated', [AutoFleetWebHookController::class, 'rideUpdated']);
    Route::post('/driver-created', [AutoFleetWebHookController::class, 'driverCreation']);
    Route::post('/driver-updated', [AutoFleetWebHookController::class, 'driverUpdated']);
    Route::post('/driver-deleted', [AutoFleetWebHookController::class, 'driverDeleted']);

    Route::post('/price-change', [AutoFleetWebHookController::class, 'priceChange']);
    Route::post('/additional-charge-added', [AutoFleetWebHookController::class, 'additionalChargeAdded']);
    Route::post('/ride-cancelled', [AutoFleetWebHookController::class, 'rideCancelled']);
    Route::post('/ride-completed', [AutoFleetWebHookController::class, 'rideCompleted']);
    Route::post('/vehicle-updated', [AutoFleetWebHookController::class, 'vehicleUpdated']);

    Route::post('/clients/onboarded', [AutofleetClientWebHookController::class, 'onboarded']);
    Route::post('/clients/updated', [AutofleetClientWebHookController::class, 'updated']);
    Route::post('/clients/deleted', [AutofleetClientWebHookController::class, 'deleted']);
    Route::post('/clients/suspended', [AutofleetClientWebHookController::class, 'suspended']);
});
