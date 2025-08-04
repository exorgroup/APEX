<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Artisan command to generate API keys for Hermes messaging service
 * 
 * File location: apex/hermes/src/Console/Commands/GenerateApiKeyCommand.php
 */

namespace Apex\Hermes\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class GenerateApiKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hermes:generate-api-key 
                            {provider : The provider (cm or messente)}
                            {--api-key= : The provider API key}
                            {--api-secret= : The provider API secret (if required)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an API key for Hermes messaging service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $provider = $this->argument('provider');

        // Validate provider
        if (!in_array($provider, ['cm', 'messente'])) {
            $this->error('Invalid provider. Must be either "cm" or "messente".');
            return 1;
        }

        // Get provider credentials
        $providerApiKey = $this->option('api-key');
        $providerApiSecret = $this->option('api-secret');

        if (!$providerApiKey) {
            $providerApiKey = $this->secret("Enter the {$provider} API key");
        }

        if (!$providerApiKey) {
            $this->error('Provider API key is required.');
            return 1;
        }

        if (!$providerApiSecret && $provider === 'messente') {
            $providerApiSecret = $this->secret("Enter the {$provider} API secret (optional)");
        }

        // Generate API key and secret
        $apiKey = $this->generateApiKey();
        $apiSecret = $this->generateApiSecret();

        try {
            // Encrypt values if encryption is enabled
            $encryptKeys = Config::get('hermes.providers.encrypt_keys', true);

            // Create the API key record
            $data = [
                'key' => $apiKey,
                'secret' => $encryptKeys ? encrypt($apiSecret) : $apiSecret,
                'provider' => $provider,
                'provider_api_key' => $encryptKeys ? encrypt($providerApiKey) : $providerApiKey,
                'provider_api_secret' => $providerApiSecret ? ($encryptKeys ? encrypt($providerApiSecret) : $providerApiSecret) : null,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Calculate signature
            $data['signature'] = $this->calculateSignature($data);

            DB::connection('hermes')->table('hermes_api_keys')->insert($data);

            $this->info('API key generated successfully!');
            $this->line('');
            $this->line('=== HERMES API CREDENTIALS ===');
            $this->line('API Key: ' . $apiKey);
            $this->line('API Secret: ' . $apiSecret);
            $this->line('Provider: ' . strtoupper($provider));
            $this->line('');
            $this->warn('Please save these credentials securely. The API secret cannot be retrieved later.');
            $this->line('');
            $this->info('Use these headers in your API requests:');
            $this->line('X-API-Key: ' . $apiKey);
            $this->line('X-API-Secret: ' . $apiSecret);

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to generate API key: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Generate a unique API key
     *
     * @return string
     */
    protected function generateApiKey(): string
    {
        do {
            $key = Str::random(64);
        } while (DB::connection('hermes')->table('hermes_api_keys')->where('key', $key)->exists());

        return $key;
    }

    /**
     * Generate an API secret
     *
     * @return string
     */
    protected function generateApiSecret(): string
    {
        return Str::random(64);
    }

    /**
     * Calculate SHA512 signature for the data
     *
     * @param array $data
     * @return string
     */
    protected function calculateSignature(array $data): string
    {
        // Remove signature field if present
        unset($data['signature']);

        // Sort by keys
        ksort($data);

        // Create string representation
        $string = json_encode($data);

        // Calculate SHA512
        return hash('sha512', $string);
    }
}
