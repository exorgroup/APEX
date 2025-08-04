<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Service for routing messages to the appropriate provider based on configuration and availability
 * 
 * File location: apex/hermes/src/Services/MessageRouter.php
 */

namespace Apex\Hermes\Services;

use Apex\Hermes\Providers\CMTelecom\CMProvider;
use Apex\Hermes\Providers\Contracts\MessageProvider;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MessageRouter
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * Initialize the message router
     */
    public function __construct()
    {
        try {
            $this->loadProviders();
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::__construct - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Load available providers
     */
    protected function loadProviders(): void
    {
        try {
            $providersConfig = Config::get('hermes.providers.providers', []);

            foreach ($providersConfig as $key => $config) {
                if ($key === 'cm' && $config['features']['sms'] === true) {
                    $this->providers['cm'] = new CMProvider();
                }
                // Add other providers here as they are implemented
                // if ($key === 'messente' && $config['features']['sms'] === true) {
                //     $this->providers['messente'] = new MessenteProvider();
                // }
            }
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::loadProviders - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a provider instance
     * 
     * @param string|null $provider
     * @return MessageProvider
     * @throws Exception
     */
    public function getProvider(?string $provider = null): MessageProvider
    {
        try {
            // Use default provider if none specified
            if (!$provider) {
                $provider = Config::get('hermes.providers.default', 'cm');
            }

            // Check if provider exists
            if (!isset($this->providers[$provider])) {
                throw new Exception("Provider '{$provider}' is not available or not configured.");
            }

            return $this->providers[$provider];
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::getProvider - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Route a message to the best available provider
     * 
     * @param string $channel
     * @param string|null $preferredProvider
     * @return MessageProvider
     * @throws Exception
     */
    public function routeMessage(string $channel, ?string $preferredProvider = null): MessageProvider
    {
        try {
            // Try preferred provider first
            if ($preferredProvider && $this->supportsChannel($preferredProvider, $channel)) {
                return $this->getProvider($preferredProvider);
            }

            // Find first available provider that supports the channel
            foreach ($this->providers as $key => $provider) {
                if ($this->supportsChannel($key, $channel)) {
                    return $provider;
                }
            }

            throw new Exception("No provider available for channel '{$channel}'");
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::routeMessage - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if a provider supports a specific channel
     * 
     * @param string $provider
     * @param string $channel
     * @return bool
     */
    protected function supportsChannel(string $provider, string $channel): bool
    {
        try {
            $features = Config::get("hermes.providers.providers.{$provider}.features", []);
            $channelKey = strtolower($channel);

            return isset($features[$channelKey]) && $features[$channelKey] === true;
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::supportsChannel - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available providers for a channel
     * 
     * @param string $channel
     * @return array
     */
    public function getAvailableProviders(string $channel): array
    {
        try {
            $available = [];

            foreach ($this->providers as $key => $provider) {
                if ($this->supportsChannel($key, $channel)) {
                    $available[] = $key;
                }
            }

            return $available;
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::getAvailableProviders - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate message parts based on content and encoding
     * 
     * @param string $message
     * @param bool $allowUnicode
     * @return array
     */
    public function calculateMessageParts(string $message, bool $allowUnicode = true): array
    {
        try {
            $length = strlen($message);
            $containsUnicode = preg_match('/[^\x00-\x7F]/', $message);

            // Determine character limits
            $singleMessageLimit = $containsUnicode ? 70 : 160;
            $multiPartLimit = $containsUnicode ? 67 : 153;

            if (!$allowUnicode && $containsUnicode) {
                throw new Exception('Message contains Unicode characters but Unicode is not allowed');
            }

            // Calculate parts
            if ($length <= $singleMessageLimit) {
                $parts = 1;
            } else {
                $parts = ceil($length / $multiPartLimit);
            }

            return [
                'length' => $length,
                'contains_unicode' => $containsUnicode,
                'parts' => $parts,
                'single_limit' => $singleMessageLimit,
                'multi_limit' => $multiPartLimit,
            ];
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::calculateMessageParts - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate phone number format
     * 
     * @param string $phoneNumber
     * @return bool
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        try {
            // E.164 format validation
            return preg_match('/^\+?[1-9]\d{1,14}$/', $phoneNumber);
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::validatePhoneNumber - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number to E.164 format
     * 
     * @param string $phoneNumber
     * @param string $defaultCountryCode
     * @return string
     */
    public function formatPhoneNumber(string $phoneNumber, string $defaultCountryCode = '1'): string
    {
        try {
            // Remove all non-numeric characters except +
            $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

            // Add + if not present
            if (strpos($phoneNumber, '+') !== 0) {
                // Add default country code if needed
                if (strlen($phoneNumber) < 10) { // Assuming minimum 10 digits for international
                    $phoneNumber = $defaultCountryCode . $phoneNumber;
                }
                $phoneNumber = '+' . $phoneNumber;
            }

            return $phoneNumber;
        } catch (\Exception $e) {
            Log::info('Error in MessageRouter::formatPhoneNumber - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return $phoneNumber;
        }
    }
}
