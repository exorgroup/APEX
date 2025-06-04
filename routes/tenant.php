<?php

declare(strict_types=1);

use App\Http\Controllers\ApexTestController;
use App\Http\Controllers\ApexTestController2;
use App\Http\Controllers\CounterTestController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;

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

    // PrimeVue Test Route
    Route::get('/primevue-test', function () {
        return Inertia::render('PrimeVueTest');
    })->name('tenant.primevue.test');

    // APEX Test Routes
    /* Route::prefix('apex')->group(function () {
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
});
