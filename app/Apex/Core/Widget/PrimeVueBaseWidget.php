<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Base widget class for PrimeVue components with license-based optimization
 * File location: app/Apex/Core/Widget/PrimeVueBaseWidget.php
 */

namespace App\Apex\Core\Widget;

use App\Apex\Core\Widget\BaseWidget;
use App\Apex\Core\Services\LicenseService;
use Illuminate\Support\Facades\Log;

abstract class PrimeVueBaseWidget extends BaseWidget
{
    protected bool $useOptimizedLoading = false;
    protected array $activeFeatures = [];
    protected array $registeredTraits = [];

    public function __construct(array $config = [])
    {
        try {
            parent::__construct($config);

            $this->useOptimizedLoading = LicenseService::canUseOptimization();
            $this->registerAvailableTraits();

            if ($this->useOptimizedLoading) {
                $this->activateNeededTraits($config);
            } else {
                $this->activateAllTraits();
            }
        } catch (\Exception $e) {
            Log::error('Error in PrimeVueBaseWidget constructor', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getSchema(): array
    {
        try {
            $baseSchema = parent::getSchema();

            foreach ($this->activeFeatures as $feature) {
                $featureSchema = $this->getFeatureSchema($feature);
                $baseSchema = array_merge_recursive($baseSchema, $featureSchema);
            }

            return $baseSchema;
        } catch (\Exception $e) {
            Log::error('Error getting schema', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
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

            foreach ($this->activeFeatures as $feature) {
                $transformed = $this->applyFeatureTransform($feature, $config, $transformed);
            }

            return $transformed;
        } catch (\Exception $e) {
            Log::error('Error transforming config', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'transform',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return parent::transform($config);
        }
    }

    protected function registerAvailableTraits(): void
    {
        try {
            $this->registeredTraits = [];
        } catch (\Exception $e) {
            Log::error('Error registering traits', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'registerAvailableTraits',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function activateAllTraits(): void
    {
        try {
            $registryClass = $this->getRegistryClass();
            $currentEdition = $this->getEdition();

            if (class_exists($registryClass)) {
                $props = $registryClass::getPropsForEdition($currentEdition);
                $this->activeFeatures = array_keys($props);
            }
        } catch (\Exception $e) {
            Log::error('Error activating all traits', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'activateAllTraits',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function activateNeededTraits(array $config): void
    {
        try {
            $registryClass = $this->getRegistryClass();
            $currentEdition = $this->getEdition();

            if (class_exists($registryClass)) {
                $availableProps = $registryClass::getPropsForEdition($currentEdition);

                foreach ($config as $key => $value) {
                    if (isset($availableProps[$key])) {
                        $this->activeFeatures[] = $key;
                    }
                }

                $this->activeFeatures = array_unique($this->activeFeatures);
            }
        } catch (\Exception $e) {
            Log::error('Error activating needed traits', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'activateNeededTraits',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function getFeatureSchema(string $feature): array
    {
        try {
            $registryClass = $this->getRegistryClass();

            if (class_exists($registryClass)) {
                return $registryClass::getPropSchema($feature);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error getting feature schema', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'getFeatureSchema',
                'feature' => $feature,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    protected function applyFeatureTransform(string $feature, array $config, array $transformed): array
    {
        try {
            return $transformed;
        } catch (\Exception $e) {
            Log::error('Error applying feature transform', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'applyFeatureTransform',
                'feature' => $feature,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $transformed;
        }
    }

    protected function isFeatureAvailableInEdition(string $featureEdition, string $currentEdition): bool
    {
        try {
            $hierarchy = ['core' => 1, 'pro' => 2, 'enterprise' => 3];
            return ($hierarchy[$featureEdition] ?? 0) <= ($hierarchy[$currentEdition] ?? 0);
        } catch (\Exception $e) {
            Log::error('Error checking feature availability', [
                'folder' => 'app/Apex/Core/Widget',
                'file' => 'PrimeVueBaseWidget.php',
                'method' => 'isFeatureAvailableInEdition',
                'featureEdition' => $featureEdition,
                'currentEdition' => $currentEdition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
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

    abstract protected function getEdition(): string;
    abstract protected function getPrimeVueComponent(): string;
    abstract protected function getRegistryClass(): string;
}
