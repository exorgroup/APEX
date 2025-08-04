<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Middleware for authenticating API requests to Hermes messaging service using API keys
 * 
 * File location: apex/hermes/src/Api/Middleware/ApiAuthentication.php
 */

namespace Apex\Hermes\Api\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Get API key from request header
            $apiKey = $request->header('X-API-Key');

            // Check if API key is provided
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key is required. Please provide X-API-Key header.'
                ], 401);
            }

            // Validate API key format
            if (!$this->isValidApiKeyFormat($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key format.'
                ], 401);
            }

            // Check if API key exists and is active
            $apiKeyRecord = DB::connection('hermes')
                ->table('hermes_api_keys')
                ->where('key', $apiKey)
                ->where('active', true)
                ->whereNull('deleted_at')
                ->first();

            if (!$apiKeyRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or inactive API key.'
                ], 401);
            }

            // Verify the secret if provided
            $apiSecret = $request->header('X-API-Secret');
            if ($apiSecret) {
                $storedSecret = Config::get('hermes.encrypt_keys', true)
                    ? decrypt($apiKeyRecord->secret)
                    : $apiKeyRecord->secret;

                if (!hash_equals($storedSecret, $apiSecret)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid API credentials.'
                    ], 401);
                }
            }

            // Add API key info to request for use in controllers
            $request->merge([
                'api_key_id' => $apiKeyRecord->id,
                'api_provider' => $apiKeyRecord->provider
            ]);

            // Log API access
            $this->logApiAccess($apiKeyRecord->id, $request);

            return $next($request);
        } catch (\Exception $e) {
            Log::info('Error in ApiAuthentication::handle - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication error occurred.'
            ], 500);
        }
    }

    /**
     * Validate API key format
     * 
     * @param string $apiKey
     * @return bool
     */
    protected function isValidApiKeyFormat(string $apiKey): bool
    {
        // Check if it's a UUID format (like CM API keys)
        if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $apiKey)) {
            return true;
        }

        // Check if it's our generated 64-character format
        if (preg_match('/^[a-zA-Z0-9]{64}$/', $apiKey)) {
            return true;
        }

        // Also accept alphanumeric with hyphens (flexible length)
        if (preg_match('/^[a-zA-Z0-9\-]{32,64}$/', $apiKey)) {
            return true;
        }

        return false;
    }

    /**
     * Log API access for auditing
     * 
     * @param int $apiKeyId
     * @param Request $request
     */
    protected function logApiAccess(int $apiKeyId, Request $request): void
    {
        try {
            // Log the API access
            Log::info('Hermes API access', [
                'api_key_id' => $apiKeyId,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::info('Error in ApiAuthentication::logApiAccess - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
        }
    }
}
