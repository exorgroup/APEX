<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Core InputText widget implementation with registry-based configuration
 * File location: app/Apex/Core/Widgets/Forms/InputText/InputTextWidget.php
 */

namespace App\Apex\Core\Widgets\Forms\InputText;

use App\Apex\Core\Widget\PrimeVueBaseWidget;
use App\Apex\Core\Registry\Components\InputTextRegistry;
use Illuminate\Support\Facades\Log;

class InputTextWidget extends PrimeVueBaseWidget
{
    public function getType(): string
    {
        try {
            return 'inputtext';
        } catch (\Exception $e) {
            Log::error('Error getting widget type', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getType',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'inputtext';
        }
    }

    protected function mapPropertyToFeature(string $property): ?string
    {
        try {
            $map = [
                'required' => 'basic_validation',
                'invalidMessage' => 'basic_validation',
                'size' => 'sizing',
                'disabled' => 'basic_state',
                'readonly' => 'basic_state',
                'modelValue' => 'two_way_binding'
            ];

            return $map[$property] ?? null;
        } catch (\Exception $e) {
            Log::error('Error mapping property to feature', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'mapPropertyToFeature',
                'property' => $property,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function usesSizing(array $config): bool
    {
        try {
            return isset($config['size']) && !empty($config['size']);
        } catch (\Exception $e) {
            Log::error('Error checking sizing usage', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'usesSizing',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function usesBasicValidation(array $config): bool
    {
        try {
            return isset($config['required']) || isset($config['invalidMessage']);
        } catch (\Exception $e) {
            Log::error('Error checking basic validation usage', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'usesBasicValidation',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function usesBasicState(array $config): bool
    {
        try {
            return isset($config['disabled']) || isset($config['readonly']);
        } catch (\Exception $e) {
            Log::error('Error checking basic state usage', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'usesBasicState',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function usesTwoWayBinding(array $config): bool
    {
        try {
            return true;
        } catch (\Exception $e) {
            Log::error('Error checking two way binding usage', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'usesTwoWayBinding',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function getPrimeVueComponent(): string
    {
        try {
            return 'PInputText';
        } catch (\Exception $e) {
            Log::error('Error getting PrimeVue component', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getPrimeVueComponent',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'PInputText';
        }
    }

    protected function getEdition(): string
    {
        try {
            return 'core';
        } catch (\Exception $e) {
            Log::error('Error getting edition', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getEdition',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'core';
        }
    }

    protected function getRegistryClass(): string
    {
        try {
            return InputTextRegistry::class;
        } catch (\Exception $e) {
            Log::error('Error getting registry class', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getRegistryClass',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return InputTextRegistry::class;
        }
    }

    public function getSchema(): array
    {
        try {
            $baseSchema = [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => [
                        'type' => 'string',
                        'enum' => ['inputtext']
                    ],
                    'id' => [
                        'type' => 'string'
                    ]
                ]
            ];

            $registryProps = InputTextRegistry::getPropsForEdition($this->getEdition());

            foreach ($registryProps as $propName => $propConfig) {
                $baseSchema['properties'][$propName] = [
                    'type' => $propConfig['type'],
                    'description' => $propConfig['description']
                ];

                if (isset($propConfig['enum'])) {
                    $baseSchema['properties'][$propName]['enum'] = $propConfig['enum'];
                }
            }

            return $baseSchema;
        } catch (\Exception $e) {
            Log::error('Error getting schema', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return parent::getSchema();
        }
    }

    public function transform(array $config): array
    {
        try {
            $transformed = parent::transform($config);

            $registryProps = InputTextRegistry::getPropsForEdition($this->getEdition());

            foreach ($config as $key => $value) {
                if (isset($registryProps[$key])) {
                    $transformed[$key] = $value;
                }
            }

            if (isset($config['required']) && $config['required']) {
                $transformed['required'] = true;
            }

            if (isset($config['size'])) {
                $transformed['sizeClasses'] = $this->getSizeClasses($config['size']);
            }

            if (isset($config['invalidMessage'])) {
                $transformed['hasValidation'] = true;
                $transformed['validationMessage'] = $config['invalidMessage'];
            }

            return $transformed;
        } catch (\Exception $e) {
            Log::error('Error transforming config', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'transform',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return parent::transform($config);
        }
    }

    private function getSizeClasses(string $size): array
    {
        try {
            return [
                'wrapper' => "apex-size-{$size}",
                'input' => $size === 'small' ? 'p-inputtext-sm' : ($size === 'large' ? 'p-inputtext-lg' : ''),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting size classes', [
                'folder' => 'app/Apex/Core/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getSizeClasses',
                'size' => $size ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
