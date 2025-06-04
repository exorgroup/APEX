<?php

use App\Http\Controllers\ApexTestController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// APEX Test Routes
Route::prefix('apex')->group(function () {
    Route::get('/test', [ApexTestController::class, 'index'])->name('apex.test');
    Route::post('/dynamic-test', [ApexTestController::class, 'dynamicTest'])->name('apex.dynamic-test');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
