<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Service for generating final registry files from curated component data
 * File location: app/Apex/PrimeReg/Services/RegistryGenerator.php
 */

namespace App\Apex\PrimeReg\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegistryGenerator
{
    /**
     * Registry class template instance
     */
    private $registryTemplate;

    /**
     * Create a new RegistryGenerator instance
     */
    public function __construct()
    {
        try {
            $templatePath = app_path('Apex/PrimeReg/Templates/RegistryClassTemplate.php');
            if (File::exists($templatePath)) {
                $this->registryTemplate = require $templatePath;
            }
        } catch (\Exception $e) {
            Log::error('Error in RegistryGenerator constructor', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate final registry from curated component data
     *
     * @param string $componentName Name of the component
     * @param array $curatedData Curated component data
     * @return array Array of generated file paths
     */
    public function generateRegistry(string $componentName, array $curatedData): array
    {
        try {
            $generatedFiles = [];

            // Clean curated data (remove excluded/deprecated items)
            $cleanData = $this->cleanCuratedData($curatedData);

            // Generate component registry JSON
            $jsonFile = $this->generateComponentRegistryJson($componentName, $cleanData);
            $generatedFiles[] = $jsonFile;

            // Generate component registry PHP class
            $phpFile = $this->generateComponentRegistryClass($componentName, $cleanData);
            $generatedFiles[] = $phpFile;

            // Update main registry
            $mainFile = $this->updateMainRegistry($componentName, $cleanData);
            $generatedFiles[] = $mainFile;

            // Generate cache file
            $cacheFile = $this->generateCacheFile($componentName, $cleanData);
            $generatedFiles[] = $cacheFile;

            return $generatedFiles;
        } catch (\Exception $e) {
            Log::error('Error generating registry', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateRegistry',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Clean curated data by removing excluded and deprecated items
     *
     * @param array $curatedData Raw curated data
     * @return array Cleaned data ready for registry
     */
    private function cleanCuratedData(array $curatedData): array
    {
        try {
            $cleanData = [];
            $sections = ['props', 'events', 'methods', 'slots'];

            foreach ($sections as $section) {
                if (!isset($curatedData[$section])) {
                    continue;
                }

                $cleanData[$section] = [];

                foreach ($curatedData[$section] as $itemName => $itemConfig) {
                    // Skip excluded or deprecated items
                    if ($itemConfig['exclude'] ?? false || $itemConfig['deprecated'] ?? false) {
                        continue;
                    }

                    // Skip unassigned items
                    if (($itemConfig['edition'] ?? 'unassigned') === 'unassigned') {
                        continue;
                    }

                    // Clean the item config
                    $cleanData[$section][$itemName] = $this->cleanItemConfig($itemConfig);
                }
            }

            return $cleanData;
        } catch (\Exception $e) {
            Log::error('Error cleaning curated data', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'cleanCuratedData',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Clean individual item configuration
     *
     * @param array $itemConfig Raw item configuration
     * @return array Cleaned item configuration
     */
    private function cleanItemConfig(array $itemConfig): array
    {
        try {
            $clean = [
                'type' => $itemConfig['type'],
                'description' => $itemConfig['description'],
                'edition' => $itemConfig['edition']
            ];

            // Add optional fields if present
            if (isset($itemConfig['required']) && $itemConfig['required']) {
                $clean['required'] = true;
            }

            if (isset($itemConfig['enum']) && is_array($itemConfig['enum'])) {
                $clean['enum'] = $itemConfig['enum'];
            }

            if (isset($itemConfig['default'])) {
                $clean['default'] = $itemConfig['default'];
            }

            return $clean;
        } catch (\Exception $e) {
            Log::error('Error cleaning item config', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'cleanItemConfig',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $itemConfig;
        }
    }

    /**
     * Generate component registry JSON file
     *
     * @param string $componentName Name of the component
     * @param array $cleanData Clean registry data
     * @return string Path to generated JSON file
     */
    private function generateComponentRegistryJson(string $componentName, array $cleanData): string
    {
        try {
            $registryPath = storage_path("app/apex/registry/{$componentName}.json");
            File::ensureDirectoryExists(dirname($registryPath));

            $registryData = [
                'component' => $componentName,
                'generated_at' => now()->toISOString(),
                'props' => $cleanData['props'] ?? [],
                'events' => $cleanData['events'] ?? [],
                'methods' => $cleanData['methods'] ?? [],
                'slots' => $cleanData['slots'] ?? []
            ];

            File::put($registryPath, json_encode($registryData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $registryPath;
        } catch (\Exception $e) {
            Log::error('Error generating component registry JSON', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateComponentRegistryJson',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate component registry PHP class
     *
     * @param string $componentName Name of the component
     * @param array $cleanData Clean registry data
     * @return string Path to generated PHP file
     */
    private function generateComponentRegistryClass(string $componentName, array $cleanData): string
    {
        try {
            $className = Str::studly($componentName) . 'Registry';
            $namespace = 'App\\Apex\\Core\\Registry\\Components';

            $classContent = $this->generateRegistryClassContent($className, $namespace, $cleanData);

            $classPath = app_path("Apex/Core/Registry/Components/{$className}.php");
            File::ensureDirectoryExists(dirname($classPath));
            File::put($classPath, $classContent);

            return $classPath;
        } catch (\Exception $e) {
            Log::error('Error generating component registry class', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateComponentRegistryClass',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate registry class content
     *
     * @param string $className Name of the class
     * @param string $namespace Class namespace
     * @param array $data Registry data
     * @return string Generated class content
     */
    private function generateRegistryClassContent(string $className, string $namespace, array $data): string
    {
        try {
            // Convert array to PHP array syntax instead of JSON
            $dataPhp = $this->arrayToPhpCode($data, 8); // 8 spaces indentation
            $timestamp = now()->format('Y-m-d H:i:s');

            return "<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Auto-generated registry class for component API definitions
 * File location: app/Apex/Core/Registry/Components/{$className}.php
 * Generated: {$timestamp}
 */

namespace {$namespace};

use Illuminate\Support\Facades\Log;

class {$className}
{
    /**
     * Component API definition data
     */
    private static array \$apiDefinition = {$dataPhp};

    /**
     * Get complete API definition for this component
     *
     * @return array Complete API definition
     */
    public static function getApiDefinition(): array
    {
        try {
            return self::\$apiDefinition;
        } catch (\\Exception \$e) {
            Log::error('Error getting API definition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getApiDefinition',
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Get properties for specific edition
     *
     * @param string \$edition Edition name (core, pro, enterprise)
     * @return array Properties available for the edition
     */
    public static function getPropsForEdition(string \$edition): array
    {
        try {
            \$props = self::\$apiDefinition['props'] ?? [];
            return array_filter(\$props, fn(\$prop) => self::isIncludedInEdition(\$prop['edition'], \$edition));
        } catch (\\Exception \$e) {
            Log::error('Error getting props for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getPropsForEdition',
                'edition' => \$edition,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Get events for specific edition
     *
     * @param string \$edition Edition name (core, pro, enterprise)
     * @return array Events available for the edition
     */
    public static function getEventsForEdition(string \$edition): array
    {
        try {
            \$events = self::\$apiDefinition['events'] ?? [];
            return array_filter(\$events, fn(\$event) => self::isIncludedInEdition(\$event['edition'], \$edition));
        } catch (\\Exception \$e) {
            Log::error('Error getting events for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getEventsForEdition',
                'edition' => \$edition,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get methods for specific edition
     *
     * @param string \$edition Edition name (core, pro, enterprise)
     * @return array Methods available for the edition
     */
    public static function getMethodsForEdition(string \$edition): array
    {
        try {
            \$methods = self::\$apiDefinition['methods'] ?? [];
            return array_filter(\$methods, fn(\$method) => self::isIncludedInEdition(\$method['edition'], \$edition));
        } catch (\\Exception \$e) {
            Log::error('Error getting methods for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getMethodsForEdition',
                'edition' => \$edition,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get slots for specific edition
     *
     * @param string \$edition Edition name (core, pro, enterprise)
     * @return array Slots available for the edition
     */
    public static function getSlotsForEdition(string \$edition): array
    {
        try {
            \$slots = self::\$apiDefinition['slots'] ?? [];
            return array_filter(\$slots, fn(\$slot) => self::isIncludedInEdition(\$slot['edition'], \$edition));
        } catch (\\Exception \$e) {
            Log::error('Error getting slots for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getSlotsForEdition',
                'edition' => \$edition,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Check if feature edition is included in current edition
     *
     * @param string \$featureEdition Edition of the feature
     * @param string \$currentEdition Current edition
     * @return bool True if feature is included
     */
    private static function isIncludedInEdition(string \$featureEdition, string \$currentEdition): bool
    {
        try {
            \$hierarchy = ['core' => 1, 'pro' => 2, 'enterprise' => 3];
            return (\$hierarchy[\$featureEdition] ?? 0) <= (\$hierarchy[\$currentEdition] ?? 0);
        } catch (\\Exception \$e) {
            Log::error('Error checking edition inclusion', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'isIncludedInEdition',
                'featureEdition' => \$featureEdition,
                'currentEdition' => \$currentEdition,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get schema for specific property
     *
     * @param string \$propName Property name
     * @return array Property schema or empty array if not found
     */
    public static function getPropSchema(string \$propName): array
    {
        try {
            return self::\$apiDefinition['props'][\$propName] ?? [];
        } catch (\\Exception \$e) {
            Log::error('Error getting prop schema', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getPropSchema',
                'propName' => \$propName,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Check if property exists in component
     *
     * @param string \$propName Property name
     * @return bool True if property exists
     */
    public static function hasProp(string \$propName): bool
    {
        try {
            return isset(self::\$apiDefinition['props'][\$propName]);
        } catch (\\Exception \$e) {
            Log::error('Error checking if prop exists', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'hasProp',
                'propName' => \$propName,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get all property names for specific edition
     *
     * @param string \$edition Edition name (core, pro, enterprise)
     * @return array Array of property names
     */
    public static function getPropNames(string \$edition): array
    {
        try {
            \$props = self::getPropsForEdition(\$edition);
            return array_keys(\$props);
        } catch (\\Exception \$e) {
            Log::error('Error getting prop names', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => '{$className}.php',
                'method' => 'getPropNames',
                'edition' => \$edition,
                'error' => \$e->getMessage(),
                'trace' => \$e->getTraceAsString()
            ]);
            return [];
        }
    }
}";
        } catch (\Exception $e) {
            Log::error('Error generating registry class content', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateRegistryClassContent',
                'className' => $className,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update main registry with component data
     *
     * @param string $componentName Name of the component
     * @param array $cleanData Clean registry data
     * @return string Path to main registry file
     */
    private function updateMainRegistry(string $componentName, array $cleanData): string
    {
        try {
            $mainRegistryPath = storage_path('app/apex/registry/main.json');
            File::ensureDirectoryExists(dirname($mainRegistryPath));

            // Load existing registry or create new one
            $mainRegistry = [];
            if (File::exists($mainRegistryPath)) {
                $existingData = File::get($mainRegistryPath);
                $mainRegistry = json_decode($existingData, true) ?? [];
            }

            // Update component data
            $mainRegistry['components'] = $mainRegistry['components'] ?? [];
            $mainRegistry['components'][Str::studly($componentName)] = $cleanData;
            $mainRegistry['updated_at'] = now()->toISOString();

            // Update metadata
            $mainRegistry['metadata'] = [
                'total_components' => count($mainRegistry['components']),
                'last_updated' => now()->toISOString(),
                'version' => '1.0.0.0'
            ];

            File::put($mainRegistryPath, json_encode($mainRegistry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $mainRegistryPath;
        } catch (\Exception $e) {
            Log::error('Error updating main registry', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'updateMainRegistry',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate cache file for component
     *
     * @param string $componentName Name of the component
     * @param array $cleanData Clean registry data
     * @return string Path to cache file
     */
    private function generateCacheFile(string $componentName, array $cleanData): string
    {
        try {
            $cachePath = storage_path("app/apex/cache/components/{$componentName}.cache");

            // Fix path separators for Windows compatibility
            $cachePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $cachePath);

            File::ensureDirectoryExists(dirname($cachePath));

            // Generate optimized cache data
            $cacheData = [
                'component' => $componentName,
                'cached_at' => now()->toISOString(),
                'editions' => $this->generateEditionCache($cleanData),
                'quick_lookup' => $this->generateQuickLookup($cleanData)
            ];

            // Serialize for faster loading
            File::put($cachePath, serialize($cacheData));

            return $cachePath;
        } catch (\Exception $e) {
            Log::error('Error generating cache file', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateCacheFile',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate edition-specific cache data
     *
     * @param array $cleanData Clean registry data
     * @return array Edition cache data
     */
    private function generateEditionCache(array $cleanData): array
    {
        try {
            $editions = ['core', 'pro', 'enterprise'];
            $editionCache = [];

            foreach ($editions as $edition) {
                $editionCache[$edition] = [
                    'props' => $this->filterByEdition($cleanData['props'] ?? [], $edition),
                    'events' => $this->filterByEdition($cleanData['events'] ?? [], $edition),
                    'methods' => $this->filterByEdition($cleanData['methods'] ?? [], $edition),
                    'slots' => $this->filterByEdition($cleanData['slots'] ?? [], $edition)
                ];
            }

            return $editionCache;
        } catch (\Exception $e) {
            Log::error('Error generating edition cache', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateEditionCache',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Generate quick lookup data for performance
     *
     * @param array $cleanData Clean registry data
     * @return array Quick lookup data
     */
    private function generateQuickLookup(array $cleanData): array
    {
        try {
            $lookup = [
                'all_props' => array_keys($cleanData['props'] ?? []),
                'all_events' => array_keys($cleanData['events'] ?? []),
                'required_props' => [],
                'core_features' => [],
                'pro_features' => [],
                'enterprise_features' => []
            ];

            // Find required props
            foreach ($cleanData['props'] ?? [] as $propName => $propConfig) {
                if ($propConfig['required'] ?? false) {
                    $lookup['required_props'][] = $propName;
                }
            }

            // Categorize features by edition
            $sections = ['props', 'events', 'methods', 'slots'];
            foreach ($sections as $section) {
                foreach ($cleanData[$section] ?? [] as $itemName => $itemConfig) {
                    $edition = $itemConfig['edition'];
                    $lookup["{$edition}_features"][] = "{$section}.{$itemName}";
                }
            }

            return $lookup;
        } catch (\Exception $e) {
            Log::error('Error generating quick lookup', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'generateQuickLookup',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Filter items by edition
     *
     * @param array $items Items to filter
     * @param string $edition Target edition
     * @return array Filtered items
     */
    private function filterByEdition(array $items, string $edition): array
    {
        try {
            $hierarchy = ['core' => 1, 'pro' => 2, 'enterprise' => 3];
            $targetLevel = $hierarchy[$edition] ?? 0;

            return array_filter($items, function ($item) use ($hierarchy, $targetLevel) {
                $itemLevel = $hierarchy[$item['edition']] ?? 0;
                return $itemLevel <= $targetLevel;
            });
        } catch (\Exception $e) {
            Log::error('Error filtering by edition', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'filterByEdition',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Convert array to properly formatted PHP array code
     *
     * @param array $array Array to convert
     * @param int $indent Base indentation level
     * @return string PHP array code
     */
    private function arrayToPhpCode(array $array, int $indent = 0): string
    {
        try {
            $spaces = str_repeat(' ', $indent);
            $innerSpaces = str_repeat(' ', $indent + 4);

            if (empty($array)) {
                return '[]';
            }

            $lines = ["["];

            foreach ($array as $key => $value) {
                $keyStr = is_string($key) ? "'" . addslashes($key) . "'" : $key;

                if (is_array($value)) {
                    $valueStr = $this->arrayToPhpCode($value, $indent + 4);
                } elseif (is_string($value)) {
                    $valueStr = "'" . addslashes($value) . "'";
                } elseif (is_bool($value)) {
                    $valueStr = $value ? 'true' : 'false';
                } elseif (is_null($value)) {
                    $valueStr = 'null';
                } else {
                    $valueStr = (string) $value;
                }

                $lines[] = "{$innerSpaces}{$keyStr} => {$valueStr},";
            }

            $lines[] = "{$spaces}]";

            return implode("\n", $lines);
        } catch (\Exception $e) {
            Log::error('Error converting array to PHP code', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'arrayToPhpCode',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return '[]';
        }
    }

    /**
     * Get generation statistics
     *
     * @param array $cleanData Clean registry data
     * @return array Generation statistics
     */
    public function getGenerationStats(array $cleanData): array
    {
        try {
            $stats = [
                'total_items' => 0,
                'by_edition' => ['core' => 0, 'pro' => 0, 'enterprise' => 0],
                'by_section' => ['props' => 0, 'events' => 0, 'methods' => 0, 'slots' => 0],
                'required_props' => 0
            ];

            $sections = ['props', 'events', 'methods', 'slots'];
            foreach ($sections as $section) {
                $sectionData = $cleanData[$section] ?? [];
                $stats['by_section'][$section] = count($sectionData);
                $stats['total_items'] += count($sectionData);

                foreach ($sectionData as $itemConfig) {
                    $edition = $itemConfig['edition'];
                    if (isset($stats['by_edition'][$edition])) {
                        $stats['by_edition'][$edition]++;
                    }

                    if ($section === 'props' && ($itemConfig['required'] ?? false)) {
                        $stats['required_props']++;
                    }
                }
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting generation stats', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'RegistryGenerator.php',
                'method' => 'getGenerationStats',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
