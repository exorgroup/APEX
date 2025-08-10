<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Auto-generated registry class for component API definitions
 * File location: app/Apex/Core/Registry/Components/InputtextRegistry.php
 * Generated: 2025-08-10 22:28:20
 */

namespace App\Apex\Core\Registry\Components;

use Illuminate\Support\Facades\Log;

class InputtextRegistry
{
    /**
     * Component API definition data
     */
    private static array $apiDefinition = [
            'props' => [
                'root' => [
                    'type' => 'string',
                    'description' => 'Auto-detected property',
                    'edition' => 'core',
                ],
                'hooks' => [
                    'type' => 'string',
                    'description' => 'Auto-detected property',
                    'edition' => 'core',
                ],
                'modelValue' => [
                    'type' => 'string',
                    'description' => 'Value of the component for v-model binding',
                    'edition' => 'core',
                ],
                'defaultValue' => [
                    'type' => 'string',
                    'description' => 'Auto-detected property',
                    'edition' => 'core',
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Name attribute of the input',
                    'edition' => 'core',
                ],
                'size' => [
                    'type' => 'string',
                    'description' => 'Size of the input field',
                    'edition' => 'core',
                    'enum' => [
                        0 => 'small',
                        1 => 'large',
                    ],
                ],
                'invalid' => [
                    'type' => 'boolean',
                    'description' => 'When present, it specifies that the component should be styled as invalid',
                    'edition' => 'core',
                ],
                'variant' => [
                    'type' => 'string',
                    'description' => 'Specifies the input variant',
                    'edition' => 'core',
                    'enum' => [
                        0 => 'filled',
                        1 => 'outlined',
                    ],
                ],
                'fluid' => [
                    'type' => 'boolean',
                    'description' => 'Spans the full width of its parent',
                    'edition' => 'core',
                ],
                'formControl' => [
                    'type' => 'string',
                    'description' => 'Auto-detected property',
                    'edition' => 'core',
                ],
                'dt' => [
                    'type' => 'string',
                    'description' => 'Auto-detected property',
                    'edition' => 'core',
                ],
                'pt' => [
                    'type' => 'object',
                    'description' => 'PrimeVue PassThrough API',
                    'edition' => 'core',
                ],
                'ptOptions' => [
                    'type' => 'object',
                    'description' => 'PassThrough options',
                    'edition' => 'core',
                ],
                'unstyled' => [
                    'type' => 'boolean',
                    'description' => 'Remove default styling',
                    'edition' => 'pro',
                ],
                'placeholder' => [
                    'type' => 'string',
                    'description' => 'Placeholder text for the input',
                    'edition' => 'core',
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'description' => 'When present, it specifies that the component should be disabled',
                    'edition' => 'core',
                ],
                'readonly' => [
                    'type' => 'boolean',
                    'description' => 'When present, it specifies that the component is read-only',
                    'edition' => 'core',
                ],
                'autocomplete' => [
                    'type' => 'string',
                    'description' => 'Used to define a string that autocompletes',
                    'edition' => 'pro',
                ],
                'accesskey' => [
                    'type' => 'string',
                    'description' => 'Keyboard shortcut key',
                    'edition' => 'enterprise',
                ],
                'tabindex' => [
                    'type' => 'number',
                    'description' => 'Tab order index',
                    'edition' => 'pro',
                ],
                'ariaLabel' => [
                    'type' => 'string',
                    'description' => 'ARIA label for accessibility',
                    'edition' => 'pro',
                ],
                'ariaLabelledby' => [
                    'type' => 'string',
                    'description' => 'ARIA labelledby reference',
                    'edition' => 'pro',
                ],
                'ariaDescribedby' => [
                    'type' => 'string',
                    'description' => 'ARIA describedby reference',
                    'edition' => 'pro',
                ],
                'class' => [
                    'type' => 'string',
                    'description' => 'CSS class names',
                    'edition' => 'pro',
                ],
                'style' => [
                    'type' => 'object',
                    'description' => 'Inline styles',
                    'edition' => 'pro',
                ],
                'maxlength' => [
                    'type' => 'number',
                    'description' => 'Maximum number of characters allowed',
                    'edition' => 'core',
                ],
                'minlength' => [
                    'type' => 'number',
                    'description' => 'Minimum number of characters required',
                    'edition' => 'core',
                ],
                'pattern' => [
                    'type' => 'string',
                    'description' => 'Regular expression pattern for validation',
                    'edition' => 'pro',
                ],
                'required' => [
                    'type' => 'boolean',
                    'description' => 'Specifies that the input field is required',
                    'edition' => 'core',
                ],
                'spellcheck' => [
                    'type' => 'boolean',
                    'description' => 'Enable/disable spell checking',
                    'edition' => 'enterprise',
                ],
                'translate' => [
                    'type' => 'string',
                    'description' => 'Translation hint for browsers',
                    'edition' => 'enterprise',
                    'enum' => [
                        0 => 'yes',
                        1 => 'no',
                    ],
                ],
                'autocapitalize' => [
                    'type' => 'string',
                    'description' => 'Controls automatic capitalization',
                    'edition' => 'enterprise',
                    'enum' => [
                        0 => 'off',
                        1 => 'none',
                        2 => 'on',
                        3 => 'sentences',
                        4 => 'words',
                        5 => 'characters',
                    ],
                ],
                'autocorrect' => [
                    'type' => 'string',
                    'description' => 'Controls automatic text correction',
                    'edition' => 'enterprise',
                    'enum' => [
                        0 => 'on',
                        1 => 'off',
                    ],
                ],
                'inputmode' => [
                    'type' => 'string',
                    'description' => 'Virtual keyboard mode hint',
                    'edition' => 'core',
                    'enum' => [
                        0 => 'none',
                        1 => 'text',
                        2 => 'decimal',
                        3 => 'numeric',
                        4 => 'tel',
                        5 => 'search',
                        6 => 'email',
                        7 => 'url',
                    ],
                ],
                'enterkeyhint' => [
                    'type' => 'string',
                    'description' => 'Enter key label hint',
                    'edition' => 'pro',
                    'enum' => [
                        0 => 'enter',
                        1 => 'done',
                        2 => 'go',
                        3 => 'next',
                        4 => 'previous',
                        5 => 'search',
                        6 => 'send',
                    ],
                ],
                'form' => [
                    'type' => 'string',
                    'description' => 'Associates input with a form element',
                    'edition' => 'core',
                ],
                'list' => [
                    'type' => 'string',
                    'description' => 'References a datalist element',
                    'edition' => 'pro',
                ],
                'multiple' => [
                    'type' => 'boolean',
                    'description' => 'Allow multiple values (for certain input types)',
                    'edition' => 'pro',
                ],
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the element',
                    'edition' => 'core',
                ],
                'title' => [
                    'type' => 'string',
                    'description' => 'Tooltip text',
                    'edition' => 'pro',
                ],
                'data-*' => [
                    'type' => 'string',
                    'description' => 'Custom data attributes',
                    'edition' => 'core',
                ],
                'dir' => [
                    'type' => 'string',
                    'description' => 'Text direction',
                    'edition' => 'core',
                    'enum' => [
                        0 => 'ltr',
                        1 => 'rtl',
                        2 => 'auto',
                    ],
                ],
                'lang' => [
                    'type' => 'string',
                    'description' => 'Language of the element content',
                    'edition' => 'pro',
                ],
                'hidden' => [
                    'type' => 'boolean',
                    'description' => 'Hide the element',
                    'edition' => 'core',
                ],
                'contenteditable' => [
                    'type' => 'string',
                    'description' => 'Whether content is editable',
                    'edition' => 'pro',
                    'enum' => [
                        0 => 'true',
                        1 => 'false',
                        2 => 'plaintext-only',
                    ],
                ],
                'draggable' => [
                    'type' => 'boolean',
                    'description' => 'Whether element is draggable',
                    'edition' => 'enterprise',
                ],
                'dropzone' => [
                    'type' => 'string',
                    'description' => 'Drop behavior',
                    'edition' => 'enterprise',
                ],
                'v-model' => [
                    'type' => 'string',
                    'description' => 'Two-way data binding',
                    'edition' => 'core',
                ],
                'v-show' => [
                    'type' => 'boolean',
                    'description' => 'Conditionally show element with CSS',
                    'edition' => 'core',
                ],
                'v-if' => [
                    'type' => 'boolean',
                    'description' => 'Conditionally render element',
                    'edition' => 'core',
                ],
                'v-else' => [
                    'type' => 'boolean',
                    'description' => 'Else block for v-if',
                    'edition' => 'core',
                ],
                'v-else-if' => [
                    'type' => 'boolean',
                    'description' => 'Else if block for v-if',
                    'edition' => 'core',
                ],
                'v-for' => [
                    'type' => 'string',
                    'description' => 'Render list of elements',
                    'edition' => 'core',
                ],
                'v-on' => [
                    'type' => 'object',
                    'description' => 'Event listeners',
                    'edition' => 'core',
                ],
                'v-bind' => [
                    'type' => 'object',
                    'description' => 'Bind attributes dynamically',
                    'edition' => 'core',
                ],
                'v-slot' => [
                    'type' => 'string',
                    'description' => 'Named slot content',
                    'edition' => 'core',
                ],
                'v-pre' => [
                    'type' => 'boolean',
                    'description' => 'Skip compilation for this element',
                    'edition' => 'core',
                ],
                'v-cloak' => [
                    'type' => 'boolean',
                    'description' => 'Hide element until Vue compilation is done',
                    'edition' => 'core',
                ],
                'v-once' => [
                    'type' => 'boolean',
                    'description' => 'Render element only once',
                    'edition' => 'core',
                ],
                'v-memo' => [
                    'type' => 'array',
                    'description' => 'Memoize template sub-tree',
                    'edition' => 'core',
                ],
            ],
            'events' => [
                'update:modelValue' => [
                    'type' => 'function',
                    'description' => 'Callback to invoke when the value changes',
                    'edition' => 'core',
                ],
                'blur' => [
                    'type' => 'function',
                    'description' => 'Callback to invoke when the component loses focus',
                    'edition' => 'pro',
                ],
                'focus' => [
                    'type' => 'function',
                    'description' => 'Callback to invoke when the component receives focus',
                    'edition' => 'pro',
                ],
                'input' => [
                    'type' => 'function',
                    'description' => 'Callback to invoke when the value changes',
                    'edition' => 'core',
                ],
                'keydown' => [
                    'type' => 'function',
                    'description' => 'Callback to invoke when a key is pressed',
                    'edition' => 'pro',
                ],
                'keyup' => [
                    'type' => 'function',
                    'description' => 'Callback to invoke when a key is released',
                    'edition' => 'pro',
                ],
                'click' => [
                    'type' => 'function',
                    'description' => 'Fired when element is clicked',
                    'edition' => 'pro',
                ],
                'dblclick' => [
                    'type' => 'function',
                    'description' => 'Fired when element is double-clicked',
                    'edition' => 'pro',
                ],
                'mousedown' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse button is pressed',
                    'edition' => 'pro',
                ],
                'mouseup' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse button is released',
                    'edition' => 'pro',
                ],
                'mouseover' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse enters element',
                    'edition' => 'pro',
                ],
                'mouseout' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse leaves element',
                    'edition' => 'pro',
                ],
                'mousemove' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse moves over element',
                    'edition' => 'pro',
                ],
                'mouseenter' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse enters element (no bubbling)',
                    'edition' => 'pro',
                ],
                'mouseleave' => [
                    'type' => 'function',
                    'description' => 'Fired when mouse leaves element (no bubbling)',
                    'edition' => 'pro',
                ],
                'contextmenu' => [
                    'type' => 'function',
                    'description' => 'Fired when right-click context menu',
                    'edition' => 'pro',
                ],
                'keypress' => [
                    'type' => 'function',
                    'description' => 'Fired when key is pressed and held',
                    'edition' => 'pro',
                ],
                'change' => [
                    'type' => 'function',
                    'description' => 'Fired when input value changes and loses focus',
                    'edition' => 'pro',
                ],
                'select' => [
                    'type' => 'function',
                    'description' => 'Fired when text is selected',
                    'edition' => 'pro',
                ],
                'submit' => [
                    'type' => 'function',
                    'description' => 'Fired when form is submitted',
                    'edition' => 'core',
                ],
                'reset' => [
                    'type' => 'function',
                    'description' => 'Fired when form is reset',
                    'edition' => 'core',
                ],
                'touchstart' => [
                    'type' => 'function',
                    'description' => 'Fired when touch begins',
                    'edition' => 'pro',
                ],
                'touchend' => [
                    'type' => 'function',
                    'description' => 'Fired when touch ends',
                    'edition' => 'pro',
                ],
                'touchmove' => [
                    'type' => 'function',
                    'description' => 'Fired when touch moves',
                    'edition' => 'pro',
                ],
                'touchcancel' => [
                    'type' => 'function',
                    'description' => 'Fired when touch is cancelled',
                    'edition' => 'pro',
                ],
                'drag' => [
                    'type' => 'function',
                    'description' => 'Fired during drag operation',
                    'edition' => 'pro',
                ],
                'dragstart' => [
                    'type' => 'function',
                    'description' => 'Fired when drag starts',
                    'edition' => 'pro',
                ],
                'dragend' => [
                    'type' => 'function',
                    'description' => 'Fired when drag ends',
                    'edition' => 'pro',
                ],
                'dragover' => [
                    'type' => 'function',
                    'description' => 'Fired when dragged over element',
                    'edition' => 'pro',
                ],
                'dragenter' => [
                    'type' => 'function',
                    'description' => 'Fired when drag enters element',
                    'edition' => 'pro',
                ],
                'dragleave' => [
                    'type' => 'function',
                    'description' => 'Fired when drag leaves element',
                    'edition' => 'pro',
                ],
                'drop' => [
                    'type' => 'function',
                    'description' => 'Fired when element is dropped',
                    'edition' => 'pro',
                ],
                'wheel' => [
                    'type' => 'function',
                    'description' => 'Fired on mouse wheel scroll',
                    'edition' => 'pro',
                ],
                'animationstart' => [
                    'type' => 'function',
                    'description' => 'Fired when CSS animation starts',
                    'edition' => 'pro',
                ],
                'animationend' => [
                    'type' => 'function',
                    'description' => 'Fired when CSS animation ends',
                    'edition' => 'pro',
                ],
                'animationiteration' => [
                    'type' => 'function',
                    'description' => 'Fired when CSS animation iteration ends',
                    'edition' => 'pro',
                ],
                'transitionstart' => [
                    'type' => 'function',
                    'description' => 'Fired when CSS transition starts',
                    'edition' => 'pro',
                ],
                'transitionend' => [
                    'type' => 'function',
                    'description' => 'Fired when CSS transition ends',
                    'edition' => 'pro',
                ],
                'loadstart' => [
                    'type' => 'function',
                    'description' => 'Fired when loading starts',
                    'edition' => 'pro',
                ],
                'load' => [
                    'type' => 'function',
                    'description' => 'Fired when resource loads',
                    'edition' => 'pro',
                ],
                'loadend' => [
                    'type' => 'function',
                    'description' => 'Fired when loading ends',
                    'edition' => 'pro',
                ],
                'error' => [
                    'type' => 'function',
                    'description' => 'Fired when error occurs',
                    'edition' => 'pro',
                ],
                'abort' => [
                    'type' => 'function',
                    'description' => 'Fired when operation is aborted',
                    'edition' => 'pro',
                ],
                'progress' => [
                    'type' => 'function',
                    'description' => 'Fired during loading progress',
                    'edition' => 'pro',
                ],
                'copy' => [
                    'type' => 'function',
                    'description' => 'Fired when content is copied',
                    'edition' => 'pro',
                ],
                'cut' => [
                    'type' => 'function',
                    'description' => 'Fired when content is cut',
                    'edition' => 'pro',
                ],
                'paste' => [
                    'type' => 'function',
                    'description' => 'Fired when content is pasted',
                    'edition' => 'pro',
                ],
                'resize' => [
                    'type' => 'function',
                    'description' => 'Fired when element is resized',
                    'edition' => 'pro',
                ],
                'scroll' => [
                    'type' => 'function',
                    'description' => 'Fired when element is scrolled',
                    'edition' => 'pro',
                ],
            ],
            'methods' => [],
            'slots' => [],
        ];

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
            Log::error('Error getting API definition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getApiDefinition',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Get properties for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Properties available for the edition
     */
    public static function getPropsForEdition(string $edition): array
    {
        try {
            $props = self::$apiDefinition['props'] ?? [];
            return array_filter($props, fn($prop) => self::isIncludedInEdition($prop['edition'], $edition));
        } catch (\Exception $e) {
            Log::error('Error getting props for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getPropsForEdition',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Get events for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Events available for the edition
     */
    public static function getEventsForEdition(string $edition): array
    {
        try {
            $events = self::$apiDefinition['events'] ?? [];
            return array_filter($events, fn($event) => self::isIncludedInEdition($event['edition'], $edition));
        } catch (\Exception $e) {
            Log::error('Error getting events for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getEventsForEdition',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get methods for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Methods available for the edition
     */
    public static function getMethodsForEdition(string $edition): array
    {
        try {
            $methods = self::$apiDefinition['methods'] ?? [];
            return array_filter($methods, fn($method) => self::isIncludedInEdition($method['edition'], $edition));
        } catch (\Exception $e) {
            Log::error('Error getting methods for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getMethodsForEdition',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get slots for specific edition
     *
     * @param string $edition Edition name (core, pro, enterprise)
     * @return array Slots available for the edition
     */
    public static function getSlotsForEdition(string $edition): array
    {
        try {
            $slots = self::$apiDefinition['slots'] ?? [];
            return array_filter($slots, fn($slot) => self::isIncludedInEdition($slot['edition'], $edition));
        } catch (\Exception $e) {
            Log::error('Error getting slots for edition', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getSlotsForEdition',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
    
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
            $hierarchy = ['core' => 1, 'pro' => 2, 'enterprise' => 3];
            return ($hierarchy[$featureEdition] ?? 0) <= ($hierarchy[$currentEdition] ?? 0);
        } catch (\Exception $e) {
            Log::error('Error checking edition inclusion', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'isIncludedInEdition',
                'featureEdition' => $featureEdition,
                'currentEdition' => $currentEdition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get schema for specific property
     *
     * @param string $propName Property name
     * @return array Property schema or empty array if not found
     */
    public static function getPropSchema(string $propName): array
    {
        try {
            return self::$apiDefinition['props'][$propName] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getting prop schema', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getPropSchema',
                'propName' => $propName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Check if property exists in component
     *
     * @param string $propName Property name
     * @return bool True if property exists
     */
    public static function hasProp(string $propName): bool
    {
        try {
            return isset(self::$apiDefinition['props'][$propName]);
        } catch (\Exception $e) {
            Log::error('Error checking if prop exists', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'hasProp',
                'propName' => $propName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

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
            Log::error('Error getting prop names', [
                'folder' => 'app/Apex/Core/Registry/Components',
                'file' => 'InputtextRegistry.php',
                'method' => 'getPropNames',
                'edition' => $edition,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}