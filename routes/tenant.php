<?php

declare(strict_types=1);

use App\Http\Controllers\ApexTestController;
use App\Http\Controllers\ApexTestController2;
use App\Http\Controllers\CounterTestController;
use App\Http\Controllers\PrimeVueTestController;
use App\Http\Controllers\PrimeVueTestController_New;
use App\Http\Controllers\PrimeRegProTestController;

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;
use App\Http\Controllers\Api\ProductController;

use App\Http\Controllers\PrimeRegTestController;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        dd(\App\Models\User::all());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Route::get('/counter-test', [CounterTestController::class, 'index'])->name('counter.test');
    Route::get('/primevue-test', [PrimeVueTestController::class, 'index'])->name('primevue.test');
    Route::get('/primevue-test-new', [PrimeVueTestController_New::class, 'index'])->name('primevue.test.new');

    Route::prefix('primereg-test')->name('primereg-test.')->group(function () {
        try {
            // Main test page - displays all widgets from traits
            Route::get('/', [PrimeRegTestController::class, 'index'])
                ->name('index');
        } catch (\Exception $e) {
            Log::error('Error defining PrimeReg test routes', [
                'file' => 'routes/web.php',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });



    // NEW: Pro edition testing routes
    Route::prefix('primereg-pro-test')->name('primereg-pro-test.')->group(function () {
        try {
            // Main pro test page - displays all pro widgets with core inheritance
            Route::get('/', [PrimeRegProTestController::class, 'index'])
                ->name('index');

            // Pro widget statistics endpoint for debugging
            Route::get('/stats', [PrimeRegProTestController::class, 'getWidgetStats'])
                ->name('stats');
        } catch (\Exception $e) {
            Log::error('Error defining PrimeReg pro test routes', [
                'file' => 'routes/tenant.php',
                'method' => 'primereg-pro-test routes',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });

    // Test route for parameter injection
    Route::post('/check-values', function (Illuminate\Http\Request $request) {
        try {
            $params = $request->input('params', []);
            $widgetId = $request->input('widgetId', 'unknown field');

            // Simple validation test
            if (empty($params[0]) || strlen($params[0]) < 3) {
                return response()->json([
                    'success' => false,
                    'state' => 'error',
                    'message' => "Field '{$widgetId}' must be at least 3 characters long",
                    'data' => ['params_received' => $params]
                ]);
            }

            return response()->json([
                'success' => true,
                'state' => 'success',
                'message' => "Field '{$widgetId}' validated successfully!",
                'data' => [
                    'params_received' => $params,
                    'validation_passed' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'state' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    });



    // PrimeVue Test Route
    /*Route::get('/primevue-test', function () {
        return Inertia::render('PrimeVueTest');
    })->name('tenant.primevue.test');

    // APEX Test Routes
     Route::prefix('apex')->group(function () {
        Route::get('/test', [ApexTestController::class, 'index'])->name('apex.test');
        Route::post('/dynamic-test', [ApexTestController::class, 'dynamicTest'])->name('apex.dynamic-test');
    });

    /*Route::get('/apex', function () {
        return Inertia::render('ApexTest');
    })->name('tenant.ApexTest.test');*/

    //Route::get('/apex2', [ApexTestController::class, 'index'])->name('apex.index');

    Route::prefix('apex')->group(function () {
        Route::get('/', [ApexTestController::class, 'index'])->name('apex.index');
        Route::post('/dynamic-test', [ApexTestController::class, 'dynamicTest'])->name('apex.dynamic-test');
    });

    Route::prefix('apexA')->group(function () {
        Route::get('/', [ApexTestController2::class, 'index'])->name('apexA.index');
        Route::post('/dynamic-test', [ApexTestController2::class, 'dynamicTest'])->name('apexA.dynamic-test');
    });

    // Product API routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/count', [ProductController::class, 'count']);  // Add this line
        Route::get('/mini', [ProductController::class, 'mini']);
        Route::get('/small', [ProductController::class, 'small']);
        Route::get('/all', [ProductController::class, 'all']);
        Route::get('/datatypes', [ProductController::class, 'datatypes']);
        // DD20250712-1930 BEGIN - Add route for products with orders (row expansion demo)
        Route::get('/with-orders', [ProductController::class, 'withOrders']);
        // DD20250712-1930 END
    });
});
