<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: API routes definition for Hermes messaging service endpoints
 * 
 * File location: apex/hermes/routes/api.php
 */

use Apex\Hermes\Api\Controllers\MessageController;
use Apex\Hermes\Api\Middleware\ApiAuthentication;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hermes API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for the Hermes messaging service. All routes
| are prefixed with 'api/v1' and protected by API authentication.
|
*/

Route::prefix('api/v1')->middleware([ApiAuthentication::class])->group(function () {

    // SMS endpoints
    Route::prefix('sms')->group(function () {
        // Send standard SMS
        Route::post('/send', [MessageController::class, 'sendSMS'])
            ->name('hermes.sms.send');

        // Send rich SMS/WhatsApp message
        Route::post('/send-rich', [MessageController::class, 'sendRichSMS'])
            ->name('hermes.sms.send-rich');

        // Get message status
        Route::get('/status', [MessageController::class, 'getMessageStatus'])
            ->name('hermes.sms.status');
    });
});
