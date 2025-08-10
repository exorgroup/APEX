<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Service for validating curation files before registry generation
 * File location: app/Apex/PrimeReg/Services/CurationValidator.php
 */

namespace App\Apex\PrimeReg\Services;

use Illuminate\Support\Facades\Log;

class CurationValidator
{
    /**
     * Valid edition values
     */
    private array $validEditions = ['core', 'pro', 'enterprise'];

    /**
     * Required metadata fields
     */
    private array $requiredMetadata = ['component', 'scanned_at'];

    /**
     * Required item fields
     */
    private array $requiredItemFields = ['type', 'description', 'edition'];

    /**
     * Valid type values
     */
    private array $validTypes = ['string', 'number', 'boolean', 'object', 'array', 'function'];

    /**
     * Validate curation data
     *
     * @param array $curatedData Curation data to validate
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public function validate(array $curatedData): array
    {
        try {
            $errors = [];

            // Validate metadata
            $metadataErrors = $this->validateMetadata($curatedData);
            $errors = array_merge($errors, $metadataErrors);

            // Validate sections
            $sectionErrors = $this->validateSections($curatedData);
            $errors = array_merge($errors, $sectionErrors);

            // Validate cross-references
            $crossRefErrors = $this->validateCrossReferences($curatedData);
            $errors = array_merge($errors, $crossRefErrors);

            return [
                'valid' => empty($errors),
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            Log::error('Error validating curation data', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validate',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'valid' => false,
                'errors' => ['Validation error: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Validate metadata section
     *
     * @param array $curatedData Curation data
     * @return array Array of validation errors
     */
    private function validateMetadata(array $curatedData): array
    {
        try {
            $errors = [];

            if (!isset($curatedData['_metadata'])) {
                $errors[] = 'Missing _metadata section';
                return $errors;
            }

            $metadata = $curatedData['_metadata'];

            // Check required metadata fields
            foreach ($this->requiredMetadata as $field) {
                if (!isset($metadata[$field]) || empty($metadata[$field])) {
                    $errors[] = "Missing required metadata field: {$field}";
                }
            }

            // Validate component name format
            if (isset($metadata['component'])) {
                if (!preg_match('/^[a-z][a-z0-9]*$/', $metadata['component'])) {
                    $errors[] = 'Component name must be lowercase alphanumeric starting with letter';
                }
            }

            // Validate timestamp format
            if (isset($metadata['scanned_at'])) {
                if (!$this->isValidTimestamp($metadata['scanned_at'])) {
                    $errors[] = 'Invalid timestamp format in scanned_at';
                }
            }

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error validating metadata', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validateMetadata',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['Error validating metadata: ' . $e->getMessage()];
        }
    }

    /**
     * Validate sections (props, events, methods, slots)
     *
     * @param array $curatedData Curation data
     * @return array Array of validation errors
     */
    private function validateSections(array $curatedData): array
    {
        try {
            $errors = [];
            $sections = ['props', 'events', 'methods', 'slots'];

            foreach ($sections as $section) {
                if (!isset($curatedData[$section])) {
                    continue; // Section is optional
                }

                $sectionData = $curatedData[$section];

                if (!is_array($sectionData)) {
                    $errors[] = "Section {$section} must be an array";
                    continue;
                }

                // Validate each item in the section
                foreach ($sectionData as $itemName => $itemConfig) {
                    $itemErrors = $this->validateSectionItem($section, $itemName, $itemConfig);
                    $errors = array_merge($errors, $itemErrors);
                }
            }

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error validating sections', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validateSections',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['Error validating sections: ' . $e->getMessage()];
        }
    }

    /**
     * Validate individual section item
     *
     * @param string $section Section name
     * @param string $itemName Item name
     * @param array $itemConfig Item configuration
     * @return array Array of validation errors
     */
    private function validateSectionItem(string $section, string $itemName, array $itemConfig): array
    {
        try {
            $errors = [];

            // Check required fields
            foreach ($this->requiredItemFields as $field) {
                if (!isset($itemConfig[$field])) {
                    $errors[] = "{$section}.{$itemName} is missing required field: {$field}";
                }
            }

            // Validate edition
            if (isset($itemConfig['edition'])) {
                if ($itemConfig['edition'] === 'unassigned') {
                    $errors[] = "{$section}.{$itemName} is still unassigned to an edition";
                } elseif (!in_array($itemConfig['edition'], $this->validEditions)) {
                    $errors[] = "{$section}.{$itemName} has invalid edition: {$itemConfig['edition']}";
                }
            }

            // Validate type
            if (isset($itemConfig['type'])) {
                if (!in_array($itemConfig['type'], $this->validTypes)) {
                    $errors[] = "{$section}.{$itemName} has invalid type: {$itemConfig['type']}";
                }
            }

            // Validate description
            if (isset($itemConfig['description'])) {
                if (empty($itemConfig['description']) || str_starts_with($itemConfig['description'], 'TODO:')) {
                    $errors[] = "{$section}.{$itemName} needs a proper description";
                }
            }

            // Validate boolean fields
            $booleanFields = ['required', 'deprecated', 'exclude'];
            foreach ($booleanFields as $field) {
                if (isset($itemConfig[$field]) && !is_bool($itemConfig[$field])) {
                    $errors[] = "{$section}.{$itemName}.{$field} must be a boolean value";
                }
            }

            // Validate enum if present
            if (isset($itemConfig['enum'])) {
                if (!is_array($itemConfig['enum']) || empty($itemConfig['enum'])) {
                    $errors[] = "{$section}.{$itemName}.enum must be a non-empty array";
                }
            }

            // Section-specific validations
            $specificErrors = $this->validateSectionSpecific($section, $itemName, $itemConfig);
            $errors = array_merge($errors, $specificErrors);

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error validating section item', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validateSectionItem',
                'section' => $section,
                'item' => $itemName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ["Error validating {$section}.{$itemName}: " . $e->getMessage()];
        }
    }

    /**
     * Validate section-specific rules
     *
     * @param string $section Section name
     * @param string $itemName Item name
     * @param array $itemConfig Item configuration
     * @return array Array of validation errors
     */
    private function validateSectionSpecific(string $section, string $itemName, array $itemConfig): array
    {
        try {
            $errors = [];

            switch ($section) {
                case 'events':
                    // Events should typically have type 'function'
                    if (isset($itemConfig['type']) && $itemConfig['type'] !== 'function') {
                        $errors[] = "{$section}.{$itemName} should typically have type 'function'";
                    }
                    break;

                case 'methods':
                    // Methods should have type 'function'
                    if (isset($itemConfig['type']) && $itemConfig['type'] !== 'function') {
                        $errors[] = "{$section}.{$itemName} must have type 'function'";
                    }
                    break;

                case 'props':
                    // Props should not have type 'function'
                    if (isset($itemConfig['type']) && $itemConfig['type'] === 'function') {
                        $errors[] = "{$section}.{$itemName} should not have type 'function'";
                    }
                    break;
            }

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error validating section specific rules', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validateSectionSpecific',
                'section' => $section,
                'item' => $itemName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ["Error validating specific rules for {$section}.{$itemName}: " . $e->getMessage()];
        }
    }

    /**
     * Validate cross-references and dependencies
     *
     * @param array $curatedData Curation data
     * @return array Array of validation errors
     */
    private function validateCrossReferences(array $curatedData): array
    {
        try {
            $errors = [];

            // Check for common inconsistencies
            $inconsistencies = $this->checkCommonInconsistencies($curatedData);
            $errors = array_merge($errors, $inconsistencies);

            // Check edition hierarchy
            $hierarchyErrors = $this->validateEditionHierarchy($curatedData);
            $errors = array_merge($errors, $hierarchyErrors);

            // Check for duplicate names across sections
            $duplicateErrors = $this->checkDuplicateNames($curatedData);
            $errors = array_merge($errors, $duplicateErrors);

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error validating cross-references', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validateCrossReferences',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['Error validating cross-references: ' . $e->getMessage()];
        }
    }

    /**
     * Check for common inconsistencies
     *
     * @param array $curatedData Curation data
     * @return array Array of validation errors
     */
    private function checkCommonInconsistencies(array $curatedData): array
    {
        try {
            $errors = [];

            // Check if modelValue prop exists for form components
            if (isset($curatedData['_metadata']['component'])) {
                $componentName = $curatedData['_metadata']['component'];
                $isFormComponent = in_array($componentName, ['inputtext', 'inputnumber', 'textarea', 'select', 'checkbox']);

                if ($isFormComponent) {
                    $props = $curatedData['props'] ?? [];
                    if (!isset($props['modelValue'])) {
                        $errors[] = "Form component {$componentName} should have modelValue prop for v-model support";
                    }

                    $events = $curatedData['events'] ?? [];
                    if (!isset($events['update:modelValue'])) {
                        $errors[] = "Form component {$componentName} should have update:modelValue event for v-model support";
                    }
                }
            }

            // Check accessibility props are in appropriate edition
            $accessibilityProps = ['accesskey', 'tabindex', 'ariaLabel', 'ariaLabelledby', 'ariaDescribedby'];
            $props = $curatedData['props'] ?? [];

            foreach ($accessibilityProps as $prop) {
                if (isset($props[$prop]) && isset($props[$prop]['edition'])) {
                    if ($props[$prop]['edition'] === 'core') {
                        $errors[] = "Accessibility prop {$prop} should typically be in 'pro' edition or higher";
                    }
                }
            }

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error checking common inconsistencies', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'checkCommonInconsistencies',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['Error checking inconsistencies: ' . $e->getMessage()];
        }
    }

    /**
     * Validate edition hierarchy makes sense
     *
     * @param array $curatedData Curation data
     * @return array Array of validation errors
     */
    private function validateEditionHierarchy(array $curatedData): array
    {
        try {
            $errors = [];
            $editionCounts = ['core' => 0, 'pro' => 0, 'enterprise' => 0];

            // Count items by edition
            $sections = ['props', 'events', 'methods', 'slots'];
            foreach ($sections as $section) {
                if (!isset($curatedData[$section])) {
                    continue;
                }

                foreach ($curatedData[$section] as $itemName => $itemConfig) {
                    if (isset($itemConfig['edition']) && !$itemConfig['exclude'] && !$itemConfig['deprecated']) {
                        $edition = $itemConfig['edition'];
                        if (isset($editionCounts[$edition])) {
                            $editionCounts[$edition]++;
                        }
                    }
                }
            }

            // Check if there are any features in each edition
            if ($editionCounts['core'] === 0) {
                $errors[] = 'No features assigned to core edition - component should have basic functionality';
            }

            // Check for logical progression
            if ($editionCounts['enterprise'] > 0 && $editionCounts['pro'] === 0) {
                $errors[] = 'Enterprise features exist but no pro features - consider adding intermediate features to pro';
            }

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error validating edition hierarchy', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'validateEditionHierarchy',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['Error validating edition hierarchy: ' . $e->getMessage()];
        }
    }

    /**
     * Check for duplicate names across sections
     *
     * @param array $curatedData Curation data
     * @return array Array of validation errors
     */
    private function checkDuplicateNames(array $curatedData): array
    {
        try {
            $errors = [];
            $allNames = [];

            $sections = ['props', 'events', 'methods', 'slots'];
            foreach ($sections as $section) {
                if (!isset($curatedData[$section])) {
                    continue;
                }

                foreach (array_keys($curatedData[$section]) as $itemName) {
                    if (isset($allNames[$itemName])) {
                        $errors[] = "Duplicate name '{$itemName}' found in {$section} and {$allNames[$itemName]}";
                    } else {
                        $allNames[$itemName] = $section;
                    }
                }
            }

            return $errors;
        } catch (\Exception $e) {
            Log::error('Error checking duplicate names', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'checkDuplicateNames',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['Error checking duplicate names: ' . $e->getMessage()];
        }
    }

    /**
     * Check if timestamp is valid ISO 8601 format
     *
     * @param string $timestamp Timestamp to validate
     * @return bool True if valid timestamp
     */
    private function isValidTimestamp(string $timestamp): bool
    {
        try {
            $date = \DateTime::createFromFormat(\DateTime::ATOM, $timestamp);
            return $date !== false && $date->format(\DateTime::ATOM) === $timestamp;
        } catch (\Exception $e) {
            Log::error('Error validating timestamp', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'isValidTimestamp',
                'timestamp' => $timestamp,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get validation statistics for curation data
     *
     * @param array $curatedData Curation data
     * @return array Statistics about the curation
     */
    public function getStatistics(array $curatedData): array
    {
        try {
            $stats = [
                'total_items' => 0,
                'by_edition' => ['core' => 0, 'pro' => 0, 'enterprise' => 0],
                'by_section' => ['props' => 0, 'events' => 0, 'methods' => 0, 'slots' => 0],
                'deprecated' => 0,
                'excluded' => 0,
                'unassigned' => 0
            ];

            $sections = ['props', 'events', 'methods', 'slots'];
            foreach ($sections as $section) {
                if (!isset($curatedData[$section])) {
                    continue;
                }

                foreach ($curatedData[$section] as $itemConfig) {
                    $stats['total_items']++;
                    $stats['by_section'][$section]++;

                    if ($itemConfig['deprecated'] ?? false) {
                        $stats['deprecated']++;
                    }

                    if ($itemConfig['exclude'] ?? false) {
                        $stats['excluded']++;
                    }

                    $edition = $itemConfig['edition'] ?? 'unassigned';
                    if ($edition === 'unassigned') {
                        $stats['unassigned']++;
                    } elseif (isset($stats['by_edition'][$edition])) {
                        $stats['by_edition'][$edition]++;
                    }
                }
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting curation statistics', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'CurationValidator.php',
                'method' => 'getStatistics',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
