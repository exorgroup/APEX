<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Service for managing APEX license detection and validation
 * File location: app/Apex/Core/Services/LicenseService.php
 */

namespace App\Apex\Core\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LicenseService
{
    /**
     * Cached license information
     */
    private static ?object $currentLicense = null;

    /**
     * Cache key for license information
     */
    private const CACHE_KEY = 'apex_license_info';

    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 60;

    /**
     * Get current license information
     *
     * @return object License information object
     */
    public static function getCurrentLicense(): object
    {
        try {
            if (self::$currentLicense === null) {
                self::$currentLicense = self::detectLicense();
            }

            return self::$currentLicense;
        } catch (\Exception $e) {
            Log::error('Error getting current license', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'getCurrentLicense',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return default core license on error
            return self::getDefaultCoreLicense();
        }
    }

    /**
     * Check if optimization features are available
     *
     * @return bool True if optimization is available
     */
    public static function canUseOptimization(): bool
    {
        try {
            $license = self::getCurrentLicense();
            return $license->optimization ?? false;
        } catch (\Exception $e) {
            Log::error('Error checking optimization availability', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'canUseOptimization',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get current edition name
     *
     * @return string Edition name (core, pro, enterprise)
     */
    public static function getEdition(): string
    {
        try {
            $license = self::getCurrentLicense();
            return $license->edition ?? 'core';
        } catch (\Exception $e) {
            Log::error('Error getting edition', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'getEdition',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'core';
        }
    }

    /**
     * Check if specific feature is available in current license
     *
     * @param string $feature Feature name to check
     * @return bool True if feature is available
     */
    public static function hasFeature(string $feature): bool
    {
        try {
            $license = self::getCurrentLicense();
            $features = $license->features ?? ['basic'];

            return in_array($feature, $features) || in_array('all', $features);
        } catch (\Exception $e) {
            Log::error('Error checking feature availability', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'hasFeature',
                'feature' => $feature,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Detect license from various sources
     *
     * @return object License information
     */
    private static function detectLicense(): object
    {
        try {
            // Try to get from cache first
            $cached = Cache::get(self::CACHE_KEY);
            if ($cached !== null) {
                return $cached;
            }

            $license = null;

            // Method 1: Check for license file
            $license = self::detectFromFile();

            if (!$license) {
                // Method 2: Check environment variables
                $license = self::detectFromEnvironment();
            }

            if (!$license) {
                // Method 3: Check database
                $license = self::detectFromDatabase();
            }

            if (!$license) {
                // Fallback: Default core license
                $license = self::getDefaultCoreLicense();
            }

            // Cache the result
            Cache::put(self::CACHE_KEY, $license, self::CACHE_DURATION);

            return $license;
        } catch (\Exception $e) {
            Log::error('Error detecting license', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'detectLicense',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::getDefaultCoreLicense();
        }
    }

    /**
     * Detect license from file
     *
     * @return object|null License object or null if not found
     */
    private static function detectFromFile(): ?object
    {
        try {
            $licenseFile = base_path('.apex-license');

            if (!File::exists($licenseFile)) {
                return null;
            }

            $content = File::get($licenseFile);
            $licenseData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Invalid JSON in license file', [
                    'folder' => 'app/Apex/Core/Services',
                    'file' => 'LicenseService.php',
                    'method' => 'detectFromFile',
                    'json_error' => json_last_error_msg()
                ]);
                return null;
            }

            return self::createLicenseObject($licenseData);
        } catch (\Exception $e) {
            Log::error('Error detecting license from file', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'detectFromFile',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Detect license from environment variables
     *
     * @return object|null License object or null if not found
     */
    private static function detectFromEnvironment(): ?object
    {
        try {
            $edition = env('APEX_EDITION');
            $licenseKey = env('APEX_LICENSE_KEY');

            if (!$edition) {
                return null;
            }

            $licenseData = [
                'edition' => $edition,
                'license_key' => $licenseKey,
                'source' => 'environment'
            ];

            return self::createLicenseObject($licenseData);
        } catch (\Exception $e) {
            Log::error('Error detecting license from environment', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'detectFromEnvironment',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Detect license from database
     *
     * @return object|null License object or null if not found
     */
    private static function detectFromDatabase(): ?object
    {
        try {
            // This would be implemented when we have the database structure
            // For now, return null
            return null;
        } catch (\Exception $e) {
            Log::error('Error detecting license from database', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'detectFromDatabase',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Create license object from data array
     *
     * @param array $licenseData Raw license data
     * @return object License object
     */
    private static function createLicenseObject(array $licenseData): object
    {
        try {
            $edition = $licenseData['edition'] ?? 'core';

            return (object) [
                'edition' => $edition,
                'features' => self::getFeaturesForEdition($edition),
                'optimization' => in_array($edition, ['pro', 'enterprise']),
                'license_key' => $licenseData['license_key'] ?? null,
                'expires_at' => $licenseData['expires_at'] ?? null,
                'source' => $licenseData['source'] ?? 'file',
                'detected_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Error creating license object', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'createLicenseObject',
                'licenseData' => $licenseData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::getDefaultCoreLicense();
        }
    }

    /**
     * Get features available for specific edition
     *
     * @param string $edition Edition name
     * @return array Available features
     */
    private static function getFeaturesForEdition(string $edition): array
    {
        try {
            $featureMap = [
                'core' => ['basic', 'widgets', 'forms'],
                'pro' => ['basic', 'widgets', 'forms', 'optimization', 'accessibility', 'i18n'],
                'enterprise' => ['all']
            ];

            return $featureMap[$edition] ?? $featureMap['core'];
        } catch (\Exception $e) {
            Log::error('Error getting features for edition', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'getFeaturesForEdition',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['basic'];
        }
    }

    /**
     * Get default core license
     *
     * @return object Default core license object
     */
    private static function getDefaultCoreLicense(): object
    {
        try {
            return (object) [
                'edition' => 'core',
                'features' => ['basic', 'widgets', 'forms'],
                'optimization' => false,
                'license_key' => null,
                'expires_at' => null,
                'source' => 'default',
                'detected_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting default core license', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'getDefaultCoreLicense',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Final fallback - return minimal object
            return (object) [
                'edition' => 'core',
                'features' => ['basic'],
                'optimization' => false
            ];
        }
    }

    /**
     * Clear license cache
     *
     * @return bool True if cache was cleared
     */
    public static function clearCache(): bool
    {
        try {
            self::$currentLicense = null;
            return Cache::forget(self::CACHE_KEY);
        } catch (\Exception $e) {
            Log::error('Error clearing license cache', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'clearCache',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Validate license key format
     *
     * @param string $licenseKey License key to validate
     * @return bool True if format is valid
     */
    public static function isValidLicenseKey(string $licenseKey): bool
    {
        try {
            // Basic format validation - adjust as needed
            return preg_match('/^APEX-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $licenseKey) === 1;
        } catch (\Exception $e) {
            Log::error('Error validating license key', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'isValidLicenseKey',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get license information for frontend
     *
     * @return array License info suitable for frontend consumption
     */
    public static function getLicenseInfoForFrontend(): array
    {
        try {
            $license = self::getCurrentLicense();

            return [
                'edition' => $license->edition,
                'features' => $license->features,
                'optimization' => $license->optimization,
                'expires_at' => $license->expires_at,
                'has_license_key' => !empty($license->license_key)
            ];
        } catch (\Exception $e) {
            Log::error('Error getting license info for frontend', [
                'folder' => 'app/Apex/Core/Services',
                'file' => 'LicenseService.php',
                'method' => 'getLicenseInfoForFrontend',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'edition' => 'core',
                'features' => ['basic'],
                'optimization' => false
            ];
        }
    }
}
