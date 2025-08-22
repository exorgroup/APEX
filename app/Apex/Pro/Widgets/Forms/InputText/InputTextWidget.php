<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: PRO InputText widget extending Core InputText with advanced props and license validation
 * File location: app/Apex/Pro/Widgets/Forms/InputText/InputTextWidget.php
 */

namespace App\Apex\Pro\Widgets\Forms\InputText;

use App\Apex\Core\Widgets\Forms\InputText\InputTextWidget as CoreInputTextWidget;
use App\Apex\Core\Services\LicenseService;
use App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits\HasEventHandling;
use App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits\HasResponseHandling;
use Illuminate\Support\Facades\Log;

class InputTextWidget extends CoreInputTextWidget
{
    use HasEventHandling, HasResponseHandling;

    /**
     * Constructor with PRO license validation
     * @param array $config Widget configuration array
     */
    public function __construct(array $config = [])
    {
        try {
            // Initialize core widget first - this sets the ID
            parent::__construct($config);

            // Check for pro license
            if (!$this->hasProLicense()) {
                Log::warning('Pro InputText widget accessed without valid license', [
                    'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                    'file' => 'InputTextWidget.php',
                    'method' => '__construct',
                    'license_edition' => LicenseService::getEdition()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in PRO InputTextWidget constructor', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get widget edition type
     * @return string Edition identifier
     */
    protected function getEdition(): string
    {
        try {
            return 'pro';
        } catch (\Exception $e) {
            Log::error('Error getting PRO edition', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getEdition',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'pro';
        }
    }

    /**
     * Check if pro license is available
     * @return bool True if pro license is valid
     */
    protected function hasProLicense(): bool
    {
        try {
            $edition = LicenseService::getEdition();
            return in_array($edition, ['pro', 'enterprise']);
        } catch (\Exception $e) {
            Log::error('Error checking pro license', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'hasProLicense',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get enhanced schema with PRO features
     * @return array Complete widget schema including PRO features
     */
    public function getSchema(): array
    {
        try {
            $baseSchema = parent::getSchema();

            // Only add pro features if license is valid
            if (!$this->hasProLicense()) {
                return $baseSchema;
            }

            // Add PRO-specific properties using trait
            $baseSchema['properties']['events'] = $this->getEventHandlingSchema();

            $baseSchema['properties']['stateConfig'] = [
                'type' => 'object',
                'description' => 'State management configuration',
                'properties' => [
                    'syncToServer' => ['type' => 'boolean', 'description' => 'Enable server synchronization'],
                    'localState' => ['type' => 'boolean', 'description' => 'Enable local state management'],
                    'conflictResolution' => [
                        'type' => 'string',
                        'enum' => ['client', 'server', 'merge', 'prompt'],
                        'description' => 'Conflict resolution strategy'
                    ]
                ]
            ];

            $baseSchema['properties']['parameterConfig'] = [
                'type' => 'object',
                'description' => 'Parameter injection configuration',
                'properties' => [
                    'contexts' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Available parameter contexts'
                    ],
                    'templates' => [
                        'type' => 'object',
                        'description' => 'Parameter templates for injection'
                    ]
                ]
            ];

            $baseSchema['properties']['advancedValidation'] = [
                'type' => 'object',
                'description' => 'Advanced validation configuration',
                'properties' => [
                    'realTimeValidation' => ['type' => 'boolean', 'description' => 'Enable real-time validation'],
                    'customRules' => [
                        'type' => 'array',
                        'description' => 'Custom validation rules'
                    ],
                    'businessRules' => [
                        'type' => 'object',
                        'description' => 'Business logic validation rules'
                    ]
                ]
            ];

            return $baseSchema;
        } catch (\Exception $e) {
            Log::error('Error getting PRO schema', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return parent::getSchema();
        }
    }

    /**
     * Transform and validate widget configuration with PRO features
     * @param array $config Raw widget configuration
     * @return array Transformed configuration with PRO features
     */
    public function transform(array $config): array
    {
        try {
            // Get base transformation
            $transformedConfig = parent::transform($config);

            // Only add pro features if license is valid
            if (!$this->hasProLicense()) {
                return $transformedConfig;
            }

            // Add PRO-specific transformations using trait
            if (isset($config['events'])) {
                $transformedConfig['events'] = $this->transformEvents($config['events']);
            }

            if (isset($config['stateConfig'])) {
                $transformedConfig['stateConfig'] = $this->transformStateConfig($config['stateConfig']);
            }

            if (isset($config['parameterConfig'])) {
                $transformedConfig['parameterConfig'] = $this->transformParameterConfig($config['parameterConfig']);
            }

            if (isset($config['advancedValidation'])) {
                $transformedConfig['advancedValidation'] = $this->transformAdvancedValidation($config['advancedValidation']);
            }

            return $transformedConfig;
        } catch (\Exception $e) {
            Log::error('Error transforming PRO config', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'transform',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return parent::transform($config);
        }
    }

    // Other transform methods remain the same...
    protected function transformStateConfig(array $stateConfig): array
    {
        try {
            $defaults = [
                'syncToServer' => false,
                'localState' => true,
                'conflictResolution' => 'client'
            ];
            return array_merge($defaults, $stateConfig);
        } catch (\Exception $e) {
            Log::error('Error transforming state config', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'transformStateConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $stateConfig;
        }
    }

    protected function transformParameterConfig(array $parameterConfig): array
    {
        try {
            $defaults = [
                'contexts' => ['widget', 'form', 'user', 'static'],
                'templates' => []
            ];
            return array_merge($defaults, $parameterConfig);
        } catch (\Exception $e) {
            Log::error('Error transforming parameter config', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'transformParameterConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $parameterConfig;
        }
    }

    protected function transformAdvancedValidation(array $advancedValidation): array
    {
        try {
            $defaults = [
                'realTimeValidation' => false,
                'customRules' => [],
                'businessRules' => []
            ];
            return array_merge($defaults, $advancedValidation);
        } catch (\Exception $e) {
            Log::error('Error transforming advanced validation', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'transformAdvancedValidation',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $advancedValidation;
        }
    }
}
