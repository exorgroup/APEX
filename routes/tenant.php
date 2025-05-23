<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
        // dd(\App\Models\User::all());
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Route::get('/apex-test', function () {
        return view('apex-test');
    })->name('apex.test');

    Route::get('/apex-widget-test', function () {
        return view('apex-widget-test', [
            'pageTitle' => 'APEX Widget Test Page',
            'testData' => [
                'dynamic_title' => 'Dynamic Title from Controller',
                'dynamic_content' => 'This content was passed from the controller.'
            ]
        ]);
    })->name('apex.widget.test');
});
