<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: PRO base widget trait providing event handling, parameter injection, state management and error handling for all PRO PrimeVue widgets
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/PrimeVueBaseWidget.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget;

use App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits\HasEventHandling;
use App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits\HasParameterInjection;
use App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits\HasStateManagement;
use App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits\HasErrorHandling;
use Illuminate\Support\Facades\Log;

trait PrimeVueBaseWidget
{
    use HasEventHandling, HasParameterInjection, HasStateManagement, HasErrorHandling;

    /**
     * Get the edition type for this widget
     * @return string The edition identifier
     */
    protected function getEdition(): string
    {
        try {
            return 'pro';
        } catch (\Exception $e) {
            Log::error('Error getting edition', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'getEdition',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'pro';
        }
    }

    /**
     * Get enhanced schema with PRO features
     * @return array Complete widget schema including PRO event and state features
     */
    public function getProSchema(): array
    {
        try {
            $baseSchema = $this->getSchema();

            // Add PRO event handling schema
            $baseSchema['properties']['events'] = $this->getEventSchema();

            // Add PRO state management schema
            $baseSchema['properties']['stateConfig'] = $this->getStateSchema();

            // Add PRO error handling schema
            $baseSchema['properties']['errorConfig'] = $this->getErrorSchema();

            // Add PRO parameter injection schema
            $baseSchema['properties']['parameterConfig'] = $this->getParameterSchema();

            return $baseSchema;
        } catch (\Exception $e) {
            Log::error('Error getting PRO schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'getProSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getSchema();
        }
    }

    /**
     * Transform configuration with PRO features
     * @param array $config Widget configuration array
     * @return array Transformed configuration with PRO features processed
     */
    public function transformPro(array $config): array
    {
        try {
            // Get base transformation
            $transformed = $this->transform($config);

            // Process PRO events
            if (isset($config['events'])) {
                $transformed['events'] = $this->processEvents($config['events']);
            }

            // Process PRO state configuration
            if (isset($config['stateConfig'])) {
                $transformed['stateConfig'] = $this->processStateConfig($config['stateConfig']);
            }

            // Process PRO error configuration
            if (isset($config['errorConfig'])) {
                $transformed['errorConfig'] = $this->processErrorConfig($config['errorConfig']);
            }

            // Process PRO parameter configuration
            if (isset($config['parameterConfig'])) {
                $transformed['parameterConfig'] = $this->processParameterConfig($config['parameterConfig']);
            }

            return $transformed;
        } catch (\Exception $e) {
            Log::error('Error transforming PRO configuration', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'transformPro',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->transform($config);
        }
    }

    /**
     * Initialize PRO widget features
     * @param array $config Widget configuration
     * @return void
     */
    protected function initializeProFeatures(array $config): void
    {
        try {
            // Initialize event handling
            $this->initializeEventHandling($config);

            // Initialize state management
            $this->initializeStateManagement($config);

            // Initialize error handling
            $this->initializeErrorHandling($config);

            // Initialize parameter injection
            $this->initializeParameterInjection($config);
        } catch (\Exception $e) {
            Log::error('Error initializing PRO features', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'initializeProFeatures',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if PRO features are available and licensed
     * @return bool True if PRO features can be used
     */
    protected function hasProLicense(): bool
    {
        try {
            return \App\Apex\Core\Services\LicenseService::canUseOptimization();
        } catch (\Exception $e) {
            Log::error('Error checking PRO license', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'hasProLicense',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get PRO widget capabilities
     * @return array List of available PRO features
     */
    public function getProCapabilities(): array
    {
        try {
            return [
                'eventHandling' => $this->hasEventHandling(),
                'parameterInjection' => $this->hasParameterInjection(),
                'stateManagement' => $this->hasStateManagement(),
                'errorHandling' => $this->hasErrorHandling(),
                'serverCommunication' => $this->hasServerCommunication(),
                'debouncing' => $this->hasDebouncing(),
                'validation' => $this->hasAdvancedValidation()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting PRO capabilities', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'getProCapabilities',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
