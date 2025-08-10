<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Template for generating component registry PHP classes
 * File location: app/Apex/PrimeReg/Templates/RegistryClassTemplate.php
 */

return [
    'namespace_template' => 'App\\Apex\\Core\\Registry\\Components',

    'class_header' => '<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Auto-generated registry class for {{COMPONENT_NAME}} component API definitions
 * File location: app/Apex/Core/Registry/Components/{{CLASS_NAME}}.php
 * Generated: {{TIMESTAMP}}
 */

namespace {{NAMESPACE}};

use Illuminate\Support\Facades\Log;',

    'class_template' => 'class {{CLASS_NAME}}
{
    /**
     * Component API definition data
     */
    private static array $apiDefinition = {{API_DATA}};

    {{METHODS}}
}',

    'methods' => [
        'getApiDefinition' => '
    /**
     * Get complete API definition for this component
     *
     * @return array Complete API definition
     */
    public static function getApiDefinition(): array
    {
        try {
            return self::$apiDefinition;
        } catch (\Exception $e) {
            Log::error(\'Error getting API definition\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'getApiDefinition\',
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return [];
        }
    }',

        'getPropsForEdition' => '
    /**
     * Get properties for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Properties available for the edition
     */
    public static function getPropsForEdition(string $edition): array
    {
        try {
            $props = self::$apiDefinition[\'props\'] ?? [];
            return array_filter($props, fn($prop) => self::isIncludedInEdition($prop[\'edition\'], $edition));
        } catch (\Exception $e) {
            Log::error(\'Error getting props for edition\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'getPropsForEdition\',
                \'edition\' => $edition,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return [];
        }
    }',

        'getEventsForEdition' => '
    /**
     * Get events for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Events available for the edition
     */
    public static function getEventsForEdition(string $edition): array
    {
        try {
            $events = self::$apiDefinition[\'events\'] ?? [];
            return array_filter($events, fn($event) => self::isIncludedInEdition($event[\'edition\'], $edition));
        } catch (\Exception $e) {
            Log::error(\'Error getting events for edition\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'getEventsForEdition\',
                \'edition\' => $edition,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return [];
        }
    }',

        'isIncludedInEdition' => '
    /**
     * Check if feature edition is included in current edition
     *
     * @param string $featureEdition Edition of the feature
     * @param string $currentEdition Current edition
     * @return bool True if feature is included
     */
    private static function isIncludedInEdition(string $featureEdition, string $currentEdition): bool
    {
        try {
            $hierarchy = [\'core\' => 1, \'pro\' => 2, \'enterprise\' => 3];
            return ($hierarchy[$featureEdition] ?? 0) <= ($hierarchy[$currentEdition] ?? 0);
        } catch (\Exception $e) {
            Log::error(\'Error checking edition inclusion\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'isIncludedInEdition\',
                \'featureEdition\' => $featureEdition,
                \'currentEdition\' => $currentEdition,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return false;
        }
    }'
    ],

    'utility_methods' => [
        'getPropSchema' => '
    /**
     * Get schema for specific property
     *
     * @param string $propName Property name
     * @return array Property schema or empty array if not found
     */
    public static function getPropSchema(string $propName): array
    {
        try {
            return self::$apiDefinition[\'props\'][$propName] ?? [];
        } catch (\Exception $e) {
            Log::error(\'Error getting prop schema\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'getPropSchema\',
                \'propName\' => $propName,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return [];
        }
    }',

        'hasProp' => '
    /**
     * Check if property exists in component
     *
     * @param string $propName Property name
     * @return bool True if property exists
     */
    public static function hasProp(string $propName): bool
    {
        try {
            return isset(self::$apiDefinition[\'props\'][$propName]);
        } catch (\Exception $e) {
            Log::error(\'Error checking if prop exists\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'hasProp\',
                \'propName\' => $propName,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return false;
        }
    }',

        'getPropNames' => '
    /**
     * Get all property names for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Array of property names
     */
    public static function getPropNames(string $edition): array
    {
        try {
            $props = self::getPropsForEdition($edition);
            return array_keys($props);
        } catch (\Exception $e) {
            Log::error(\'Error getting prop names\', [
                \'folder\' => \'app/Apex/Core/Registry/Components\',
                \'file\' => \'{{CLASS_NAME}}.php\',
                \'method\' => \'getPropNames\',
                \'edition\' => $edition,
                \'error\' => $e->getMessage(),
                \'trace\' => $e->getTraceAsString()
            ]);
            return [];
        }
    }'
    ]
];
