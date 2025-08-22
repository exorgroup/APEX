<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Response handling trait for PRO widgets providing multi-state response processing
 * File location: app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits/HasResponseHandling.php
 */

namespace App\Apex\Pro\Widget\PrimeVueBaseWidget\Traits;

use Illuminate\Support\Facades\Log;

trait HasResponseHandling
{
    /**
     * Transform response configuration for event handling
     * @param array $responseConfig Response configuration array
     * @return array Transformed response configuration
     */
    protected function transformResponseConfig(array $responseConfig): array
    {
        try {
            $transformedConfig = [];

            // Define default states if not provided
            $defaultStates = ['success', 'info', 'warn', 'error'];

            foreach ($defaultStates as $state) {
                if (isset($responseConfig[$state])) {
                    $transformedConfig[$state] = $this->processStateConfig($responseConfig[$state], $state);
                } else {
                    // Provide sensible defaults for missing states
                    $transformedConfig[$state] = $this->getDefaultStateConfig($state);
                }
            }

            return $transformedConfig;
        } catch (\Exception $e) {
            Log::error('Error transforming response config', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'transformResponseConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $responseConfig;
        }
    }

    /**
     * Process individual state configuration
     * @param array $stateConfig Configuration for a specific state
     * @param string $stateName Name of the state (success, info, warn, error)
     * @return array Processed state configuration
     */
    protected function processStateConfig(array $stateConfig, string $stateName): array
    {
        try {
            $type = $stateConfig['type'] ?? 'alert';
            $processedConfig = [
                'type' => $type,
                'state' => $stateName
            ];

            switch ($type) {
                case 'server':
                    $processedConfig['server'] = $stateConfig['server'] ?? '';
                    $processedConfig['params'] = $stateConfig['params'] ?? [];
                    // Process parameter templates if any
                    if (!empty($processedConfig['params'])) {
                        $processedConfig['params'] = array_map(function ($param) {
                            return $this->processResponseParameter($param);
                        }, $processedConfig['params']);
                    }
                    break;

                case 'toast':
                    $processedConfig['severity'] = $stateConfig['severity'] ?? $this->getDefaultSeverity($stateName);
                    $processedConfig['position'] = $stateConfig['position'] ?? 'top-right';
                    $processedConfig['life'] = $stateConfig['life'] ?? 3000;
                    $processedConfig['closable'] = $stateConfig['closable'] ?? true;
                    break;

                case 'modal':
                    $processedConfig['title'] = $stateConfig['title'] ?? ucfirst($stateName);
                    $processedConfig['buttonText'] = $stateConfig['buttonText'] ?? 'OK';
                    $processedConfig['buttonSeverity'] = $stateConfig['buttonSeverity'] ?? $this->getDefaultSeverity($stateName);
                    $processedConfig['image'] = $stateConfig['image'] ?? '';
                    $processedConfig['width'] = $stateConfig['width'] ?? '400px';
                    $processedConfig['modal'] = $stateConfig['modal'] ?? true;
                    $processedConfig['closable'] = $stateConfig['closable'] ?? true;
                    break;

                case 'alert':
                default:
                    // Alert has no additional configuration
                    break;
            }

            return $processedConfig;
        } catch (\Exception $e) {
            Log::error('Error processing state config', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'processStateConfig',
                'stateName' => $stateName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $stateConfig;
        }
    }

    /**
     * Process response parameter for template injection
     * @param string $param Parameter that may contain templates
     * @return string Processed parameter
     */
    protected function processResponseParameter(string $param): string
    {
        try {
            // Process parameter templates similar to event parameters
            if (preg_match('/\{\{(.+?)\}\}/', $param, $matches)) {
                $template = $matches[1];
                return $this->convertTemplateToJS($template);
            }

            return $param;
        } catch (\Exception $e) {
            Log::error('Error processing response parameter', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'processResponseParameter',
                'param' => $param,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $param;
        }
    }

    /**
     * Get default configuration for a state
     * @param string $stateName State name (success, info, warn, error)
     * @return array Default configuration
     */
    protected function getDefaultStateConfig(string $stateName): array
    {
        try {
            $defaults = [
                'success' => [
                    'type' => 'toast',
                    'severity' => 'success',
                    'position' => 'top-right',
                    'life' => 3000,
                    'state' => 'success'
                ],
                'info' => [
                    'type' => 'toast',
                    'severity' => 'info',
                    'position' => 'top-right',
                    'life' => 5000,
                    'state' => 'info'
                ],
                'warn' => [
                    'type' => 'toast',
                    'severity' => 'warn',
                    'position' => 'bottom-right',
                    'life' => 5000,
                    'state' => 'warn'
                ],
                'error' => [
                    'type' => 'alert',
                    'state' => 'error'
                ]
            ];

            return $defaults[$stateName] ?? $defaults['error'];
        } catch (\Exception $e) {
            Log::error('Error getting default state config', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'getDefaultStateConfig',
                'stateName' => $stateName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['type' => 'alert', 'state' => $stateName];
        }
    }

    /**
     * Get default severity for state
     * @param string $stateName State name
     * @return string Default severity
     */
    protected function getDefaultSeverity(string $stateName): string
    {
        try {
            $severityMap = [
                'success' => 'success',
                'info' => 'info',
                'warn' => 'warn',
                'error' => 'error'
            ];

            return $severityMap[$stateName] ?? 'info';
        } catch (\Exception $e) {
            Log::error('Error getting default severity', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'getDefaultSeverity',
                'stateName' => $stateName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'info';
        }
    }

    /**
     * Get response handling schema for widget validation
     * @return array JSON schema definition for response handling
     */
    protected function getResponseHandlingSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Response handling configuration for different states',
                'properties' => [
                    'success' => $this->getStateSchema(),
                    'info' => $this->getStateSchema(),
                    'warn' => $this->getStateSchema(),
                    'error' => $this->getStateSchema()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting response handling schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'getResponseHandlingSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get schema for individual state configuration
     * @return array State configuration schema
     */
    protected function getStateSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'properties' => [
                    'type' => [
                        'type' => 'string',
                        'enum' => ['server', 'alert', 'toast', 'modal'],
                        'description' => 'Response display type'
                    ],
                    'server' => [
                        'type' => 'string',
                        'description' => 'Server URL for server type responses'
                    ],
                    'params' => [
                        'type' => 'array',
                        'description' => 'Parameters for server requests'
                    ],
                    'severity' => [
                        'type' => 'string',
                        'enum' => ['success', 'info', 'warn', 'error', 'secondary', 'contrast'],
                        'description' => 'Severity level for toast/modal'
                    ],
                    'position' => [
                        'type' => 'string',
                        'enum' => ['top-left', 'top-right', 'bottom-left', 'bottom-right'],
                        'description' => 'Position for toast messages'
                    ],
                    'title' => [
                        'type' => 'string',
                        'description' => 'Title for modal dialogs'
                    ],
                    'buttonText' => [
                        'type' => 'string',
                        'description' => 'Button text for modal dialogs'
                    ],
                    'buttonSeverity' => [
                        'type' => 'string',
                        'enum' => ['success', 'info', 'warn', 'error', 'secondary', 'contrast'],
                        'description' => 'Button severity for modal dialogs'
                    ],
                    'image' => [
                        'type' => 'string',
                        'description' => 'Image URL for modal dialogs'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting state schema', [
                'folder' => 'app/Apex/Pro/Widget/PrimeVueBaseWidget/Traits',
                'file' => 'HasResponseHandling.php',
                'method' => 'getStateSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
