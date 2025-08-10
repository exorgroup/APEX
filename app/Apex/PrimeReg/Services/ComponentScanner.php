<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Enhanced scanner for PrimeVue 4.x component structure with HTML & Vue attributes
 * File location: app/Apex/PrimeReg/Services/ComponentScanner.php
 */

namespace App\Apex\PrimeReg\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ComponentScanner
{
    /**
     * Path to PrimeVue node modules
     */
    private string $primeVuePath;

    /**
     * Create a new ComponentScanner instance
     */
    public function __construct()
    {
        try {
            $this->primeVuePath = base_path('node_modules/primevue');
        } catch (\Exception $e) {
            Log::error('Error in ComponentScanner constructor', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check if PrimeVue is installed in node_modules
     *
     * @return bool True if PrimeVue is installed, false otherwise
     */
    public function isPrimeVueInstalled(): bool
    {
        try {
            return File::exists($this->primeVuePath) && File::isDirectory($this->primeVuePath);
        } catch (\Exception $e) {
            Log::error('Error checking PrimeVue installation', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'isPrimeVueInstalled',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get all available PrimeVue components
     *
     * @return array List of component names
     */
    public function getAllComponents(): array
    {
        try {
            if (!$this->isPrimeVueInstalled()) {
                return [];
            }

            $components = [];
            $directories = File::directories($this->primeVuePath);

            foreach ($directories as $dir) {
                $componentName = basename($dir);

                // Skip non-component directories
                if (in_array($componentName, ['core', 'themes', 'icons', 'utils', 'config', 'passthrough', 'base'])) {
                    continue;
                }

                // Check if it has component files
                if ($this->hasComponentFiles($dir)) {
                    $components[] = $componentName;
                }
            }

            return $components;
        } catch (\Exception $e) {
            Log::error('Error getting all components', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'getAllComponents',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Scan a specific component and extract API information
     *
     * @param string $componentName Name of the component to scan
     * @return array Raw API data extracted from the component
     */
    public function scanComponent(string $componentName): array
    {
        try {
            $componentPath = "{$this->primeVuePath}/{$componentName}";

            if (!File::exists($componentPath)) {
                Log::warning('Component path not found', [
                    'folder' => 'app/Apex/PrimeReg/Services',
                    'file' => 'ComponentScanner.php',
                    'method' => 'scanComponent',
                    'component' => $componentName,
                    'path' => $componentPath
                ]);
                return [];
            }

            $rawData = [
                'props' => [],
                'events' => [],
                'methods' => [],
                'slots' => []
            ];

            // Method 1: Parse the main TypeScript definition file
            $mainDtsFile = "{$componentPath}/index.d.ts";
            if (File::exists($mainDtsFile)) {
                $dtsData = $this->parsePrimeVue4TypeScript($mainDtsFile, $componentName);
                $rawData = $this->mergeComponentData($rawData, $dtsData);
            }

            // Method 2: Parse Vue component files
            $vueFiles = File::glob("{$componentPath}/*.vue");
            foreach ($vueFiles as $vueFile) {
                $vueData = $this->parseVueComponent($vueFile, $componentName);
                $rawData = $this->mergeComponentData($rawData, $vueData);
            }

            // Method 3: Add known patterns for this component
            $knownData = $this->getKnownComponentPatterns($componentName);
            $rawData = $this->mergeComponentData($rawData, $knownData);

            // Method 4: Add common PrimeVue patterns
            $rawData = $this->addCommonPatterns($rawData, $componentName);

            // NEW: Method 5: Add HTML input attributes
            $rawData = $this->addHtmlInputAttributes($rawData, $componentName);

            // NEW: Method 6: Add Vue-specific events
            $rawData = $this->addVueSpecificEvents($rawData, $componentName);

            // NEW: Method 7: Add Vue directives
            $rawData = $this->addVueDirectives($rawData);

            return $rawData;
        } catch (\Exception $e) {
            Log::error('Error scanning component', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'scanComponent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Add HTML input attributes that work with InputText
     */
    private function addHtmlInputAttributes(array $rawData, string $componentName): array
    {
        if ($componentName !== 'inputtext') {
            return $rawData;
        }

        $htmlAttributes = [
            // Form attributes
            'maxlength' => [
                'type' => 'number',
                'description' => 'Maximum number of characters allowed',
                'source' => 'html'
            ],
            'minlength' => [
                'type' => 'number',
                'description' => 'Minimum number of characters required',
                'source' => 'html'
            ],
            'pattern' => [
                'type' => 'string',
                'description' => 'Regular expression pattern for validation',
                'source' => 'html'
            ],
            'required' => [
                'type' => 'boolean',
                'description' => 'Specifies that the input field is required',
                'source' => 'html'
            ],
            'spellcheck' => [
                'type' => 'boolean',
                'description' => 'Enable/disable spell checking',
                'source' => 'html'
            ],
            'translate' => [
                'type' => 'string',
                'description' => 'Translation hint for browsers',
                'enum' => ['yes', 'no'],
                'source' => 'html'
            ],
            'autocapitalize' => [
                'type' => 'string',
                'description' => 'Controls automatic capitalization',
                'enum' => ['off', 'none', 'on', 'sentences', 'words', 'characters'],
                'source' => 'html'
            ],
            'autocorrect' => [
                'type' => 'string',
                'description' => 'Controls automatic text correction',
                'enum' => ['on', 'off'],
                'source' => 'html'
            ],
            'inputmode' => [
                'type' => 'string',
                'description' => 'Virtual keyboard mode hint',
                'enum' => ['none', 'text', 'decimal', 'numeric', 'tel', 'search', 'email', 'url'],
                'source' => 'html'
            ],
            'enterkeyhint' => [
                'type' => 'string',
                'description' => 'Enter key label hint',
                'enum' => ['enter', 'done', 'go', 'next', 'previous', 'search', 'send'],
                'source' => 'html'
            ],
            'form' => [
                'type' => 'string',
                'description' => 'Associates input with a form element',
                'source' => 'html'
            ],
            'list' => [
                'type' => 'string',
                'description' => 'References a datalist element',
                'source' => 'html'
            ],
            'multiple' => [
                'type' => 'boolean',
                'description' => 'Allow multiple values (for certain input types)',
                'source' => 'html'
            ],

            // Global HTML attributes commonly used
            'id' => [
                'type' => 'string',
                'description' => 'Unique identifier for the element',
                'source' => 'html'
            ],
            'title' => [
                'type' => 'string',
                'description' => 'Tooltip text',
                'source' => 'html'
            ],
            'data-*' => [
                'type' => 'string',
                'description' => 'Custom data attributes',
                'source' => 'html'
            ],
            'dir' => [
                'type' => 'string',
                'description' => 'Text direction',
                'enum' => ['ltr', 'rtl', 'auto'],
                'source' => 'html'
            ],
            'lang' => [
                'type' => 'string',
                'description' => 'Language of the element content',
                'source' => 'html'
            ],
            'hidden' => [
                'type' => 'boolean',
                'description' => 'Hide the element',
                'source' => 'html'
            ],
            'contenteditable' => [
                'type' => 'string',
                'description' => 'Whether content is editable',
                'enum' => ['true', 'false', 'plaintext-only'],
                'source' => 'html'
            ],
            'draggable' => [
                'type' => 'boolean',
                'description' => 'Whether element is draggable',
                'source' => 'html'
            ],
            'dropzone' => [
                'type' => 'string',
                'description' => 'Drop behavior',
                'source' => 'html'
            ]
        ];

        // Merge HTML attributes with existing props
        foreach ($htmlAttributes as $attr => $config) {
            if (!isset($rawData['props'][$attr])) {
                $rawData['props'][$attr] = $config;
            }
        }

        return $rawData;
    }

    /**
     * Add Vue-specific events and handlers
     */
    private function addVueSpecificEvents(array $rawData, string $componentName): array
    {
        $vueEvents = [
            // Mouse events
            'click' => 'Fired when element is clicked',
            'dblclick' => 'Fired when element is double-clicked',
            'mousedown' => 'Fired when mouse button is pressed',
            'mouseup' => 'Fired when mouse button is released',
            'mouseover' => 'Fired when mouse enters element',
            'mouseout' => 'Fired when mouse leaves element',
            'mousemove' => 'Fired when mouse moves over element',
            'mouseenter' => 'Fired when mouse enters element (no bubbling)',
            'mouseleave' => 'Fired when mouse leaves element (no bubbling)',
            'contextmenu' => 'Fired when right-click context menu',

            // Keyboard events (in addition to existing)
            'keypress' => 'Fired when key is pressed and held',

            // Form events
            'change' => 'Fired when input value changes and loses focus',
            'select' => 'Fired when text is selected',
            'submit' => 'Fired when form is submitted',
            'reset' => 'Fired when form is reset',

            // Touch events
            'touchstart' => 'Fired when touch begins',
            'touchend' => 'Fired when touch ends',
            'touchmove' => 'Fired when touch moves',
            'touchcancel' => 'Fired when touch is cancelled',

            // Drag events
            'drag' => 'Fired during drag operation',
            'dragstart' => 'Fired when drag starts',
            'dragend' => 'Fired when drag ends',
            'dragover' => 'Fired when dragged over element',
            'dragenter' => 'Fired when drag enters element',
            'dragleave' => 'Fired when drag leaves element',
            'drop' => 'Fired when element is dropped',

            // Wheel events
            'wheel' => 'Fired on mouse wheel scroll',

            // Animation/Transition events
            'animationstart' => 'Fired when CSS animation starts',
            'animationend' => 'Fired when CSS animation ends',
            'animationiteration' => 'Fired when CSS animation iteration ends',
            'transitionstart' => 'Fired when CSS transition starts',
            'transitionend' => 'Fired when CSS transition ends',

            // Media events (for applicable components)
            'loadstart' => 'Fired when loading starts',
            'load' => 'Fired when resource loads',
            'loadend' => 'Fired when loading ends',
            'error' => 'Fired when error occurs',
            'abort' => 'Fired when operation is aborted',
            'progress' => 'Fired during loading progress',

            // Clipboard events
            'copy' => 'Fired when content is copied',
            'cut' => 'Fired when content is cut',
            'paste' => 'Fired when content is pasted',

            // Other useful events
            'resize' => 'Fired when element is resized',
            'scroll' => 'Fired when element is scrolled'
        ];

        foreach ($vueEvents as $event => $description) {
            if (!isset($rawData['events'][$event])) {
                $rawData['events'][$event] = [
                    'type' => 'function',
                    'description' => $description,
                    'source' => 'vue'
                ];
            }
        }

        return $rawData;
    }

    /**
     * Add Vue directives as props
     */
    private function addVueDirectives(array $rawData): array
    {
        $vueDirectives = [
            'v-model' => [
                'type' => 'any',
                'description' => 'Two-way data binding',
                'source' => 'vue'
            ],
            'v-show' => [
                'type' => 'boolean',
                'description' => 'Conditionally show element with CSS',
                'source' => 'vue'
            ],
            'v-if' => [
                'type' => 'boolean',
                'description' => 'Conditionally render element',
                'source' => 'vue'
            ],
            'v-else' => [
                'type' => 'boolean',
                'description' => 'Else block for v-if',
                'source' => 'vue'
            ],
            'v-else-if' => [
                'type' => 'boolean',
                'description' => 'Else if block for v-if',
                'source' => 'vue'
            ],
            'v-for' => [
                'type' => 'string',
                'description' => 'Render list of elements',
                'source' => 'vue'
            ],
            'v-on' => [
                'type' => 'object',
                'description' => 'Event listeners',
                'source' => 'vue'
            ],
            'v-bind' => [
                'type' => 'object',
                'description' => 'Bind attributes dynamically',
                'source' => 'vue'
            ],
            'v-slot' => [
                'type' => 'string',
                'description' => 'Named slot content',
                'source' => 'vue'
            ],
            'v-pre' => [
                'type' => 'boolean',
                'description' => 'Skip compilation for this element',
                'source' => 'vue'
            ],
            'v-cloak' => [
                'type' => 'boolean',
                'description' => 'Hide element until Vue compilation is done',
                'source' => 'vue'
            ],
            'v-once' => [
                'type' => 'boolean',
                'description' => 'Render element only once',
                'source' => 'vue'
            ],
            'v-memo' => [
                'type' => 'array',
                'description' => 'Memoize template sub-tree',
                'source' => 'vue'
            ]
        ];

        foreach ($vueDirectives as $directive => $config) {
            if (!isset($rawData['props'][$directive])) {
                $rawData['props'][$directive] = $config;
            }
        }

        return $rawData;
    }

    /**
     * Parse PrimeVue 4.x TypeScript definition file
     *
     * @param string $filePath Path to TypeScript file
     * @param string $componentName Component name
     * @return array Parsed TypeScript data
     */
    private function parsePrimeVue4TypeScript(string $filePath, string $componentName): array
    {
        try {
            $content = File::get($filePath);

            $data = [
                'props' => [],
                'events' => [],
                'methods' => [],
                'slots' => []
            ];

            // Extract props from Props interface
            $data['props'] = array_merge($data['props'], $this->extractPropsFromContent($content, $componentName));

            // Extract events from Emits interface
            $data['events'] = array_merge($data['events'], $this->extractEventsFromContent($content, $componentName));

            // Extract slots from Slots interface
            $data['slots'] = array_merge($data['slots'], $this->extractSlotsFromContent($content, $componentName));

            // Extract methods from exposed methods
            $data['methods'] = array_merge($data['methods'], $this->extractMethodsFromContent($content, $componentName));

            return $data;
        } catch (\Exception $e) {
            Log::error('Error parsing PrimeVue 4 TypeScript', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'parsePrimeVue4TypeScript',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Extract props from TypeScript content
     *
     * @param string $content TypeScript content
     * @param string $componentName Component name
     * @return array Extracted props
     */
    private function extractPropsFromContent(string $content, string $componentName): array
    {
        try {
            $props = [];

            // Pattern 1: Find Props interface
            $propsPattern = '/export\s+interface\s+' . ucfirst($componentName) . 'Props\s+extends\s+([^{]+)\s*{([^}]+)}/s';
            if (preg_match($propsPattern, $content, $matches)) {
                $extendsFrom = trim($matches[1]);
                $propsBody = $matches[2];

                // Parse the props in this interface
                $props = array_merge($props, $this->parsePropsFromInterfaceBody($propsBody));

                // Try to resolve extended interfaces
                $extendedProps = $this->resolveExtendedProps($content, $extendsFrom);
                $props = array_merge($props, $extendedProps);
            }

            // Pattern 2: Look for direct prop definitions
            if (preg_match_all('/(\w+)\?\s*:\s*([^;,\n]+)/', $content, $propMatches, PREG_SET_ORDER)) {
                foreach ($propMatches as $match) {
                    $propName = $match[1];
                    $propType = trim($match[2]);

                    if (!isset($props[$propName])) {
                        $props[$propName] = [
                            'type' => $this->convertTypeScriptType($propType),
                            'required' => !str_contains($match[0], '?'),
                            'description' => "Auto-detected property",
                            'source' => 'typescript'
                        ];

                        // Extract enum values if present
                        if ($this->isEnumType($propType)) {
                            $props[$propName]['enum'] = $this->extractEnumValues($propType);
                        }
                    }
                }
            }

            return $props;
        } catch (\Exception $e) {
            Log::error('Error extracting props from content', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'extractPropsFromContent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Parse props from interface body
     *
     * @param string $interfaceBody Interface body content
     * @return array Parsed props
     */
    private function parsePropsFromInterfaceBody(string $interfaceBody): array
    {
        try {
            $props = [];

            // Match property definitions with comments
            preg_match_all('/\/\*\*\s*\*\s*([^*]+)\s*\*\/\s*(\w+)\?\s*:\s*([^;,\n]+)/s', $interfaceBody, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $description = trim($match[1]);
                $propName = $match[2];
                $propType = trim($match[3]);

                $props[$propName] = [
                    'type' => $this->convertTypeScriptType($propType),
                    'required' => false, // Most PrimeVue props are optional
                    'description' => $description,
                    'source' => 'typescript'
                ];

                if ($this->isEnumType($propType)) {
                    $props[$propName]['enum'] = $this->extractEnumValues($propType);
                }
            }

            // Also match properties without comments
            preg_match_all('/(\w+)\?\s*:\s*([^;,\n]+)/', $interfaceBody, $propMatches, PREG_SET_ORDER);

            foreach ($propMatches as $match) {
                $propName = $match[1];
                $propType = trim($match[2]);

                if (!isset($props[$propName])) {
                    $props[$propName] = [
                        'type' => $this->convertTypeScriptType($propType),
                        'required' => false,
                        'description' => "Property: {$propName}",
                        'source' => 'typescript'
                    ];

                    if ($this->isEnumType($propType)) {
                        $props[$propName]['enum'] = $this->extractEnumValues($propType);
                    }
                }
            }

            return $props;
        } catch (\Exception $e) {
            Log::error('Error parsing props from interface body', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'parsePropsFromInterfaceBody',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Extract events from TypeScript content
     *
     * @param string $content TypeScript content
     * @param string $componentName Component name
     * @return array Extracted events
     */
    private function extractEventsFromContent(string $content, string $componentName): array
    {
        try {
            $events = [];

            // Pattern 1: Look for Emits interface
            $emitsPattern = '/export\s+interface\s+' . ucfirst($componentName) . 'Emits\s*{([^}]+)}/s';
            if (preg_match($emitsPattern, $content, $matches)) {
                $emitsBody = $matches[1];
                $events = array_merge($events, $this->parseEventsFromInterfaceBody($emitsBody));
            }

            // Pattern 2: Look for event definitions in DefineComponent
            if (preg_match('/DefineComponent<[^,]+,\s*[^,]+,\s*[^,]+,\s*([^,>]+)/', $content, $matches)) {
                $emitsType = trim($matches[1]);
                $events = array_merge($events, $this->parseEventsFromType($emitsType));
            }

            return $events;
        } catch (\Exception $e) {
            Log::error('Error extracting events from content', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'extractEventsFromContent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Parse events from interface body
     *
     * @param string $interfaceBody Interface body content
     * @return array Parsed events
     */
    private function parseEventsFromInterfaceBody(string $interfaceBody): array
    {
        try {
            $events = [];

            // Match event definitions
            preg_match_all('/(\w+):\s*\[([^\]]*)\]/', $interfaceBody, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $eventName = $match[1];
                $eventParams = $match[2];

                $events[$eventName] = [
                    'description' => "Event: {$eventName}",
                    'params' => trim($eventParams),
                    'source' => 'typescript'
                ];
            }

            return $events;
        } catch (\Exception $e) {
            Log::error('Error parsing events from interface body', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'parseEventsFromInterfaceBody',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Extract slots from TypeScript content
     *
     * @param string $content TypeScript content
     * @param string $componentName Component name
     * @return array Extracted slots
     */
    private function extractSlotsFromContent(string $content, string $componentName): array
    {
        try {
            $slots = [];

            // Look for Slots interface
            $slotsPattern = '/export\s+interface\s+' . ucfirst($componentName) . 'Slots\s*{([^}]+)}/s';
            if (preg_match($slotsPattern, $content, $matches)) {
                $slotsBody = $matches[1];
                $slots = array_merge($slots, $this->parseSlotsFromInterfaceBody($slotsBody));
            }

            return $slots;
        } catch (\Exception $e) {
            Log::error('Error extracting slots from content', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'extractSlotsFromContent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Parse slots from interface body
     *
     * @param string $interfaceBody Interface body content
     * @return array Parsed slots
     */
    private function parseSlotsFromInterfaceBody(string $interfaceBody): array
    {
        try {
            $slots = [];

            preg_match_all('/(\w+)\s*\?\s*:\s*\([^)]*\)\s*=>\s*any/', $interfaceBody, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $slotName = $match[1];

                $slots[$slotName] = [
                    'description' => "Slot: {$slotName}",
                    'source' => 'typescript'
                ];
            }

            return $slots;
        } catch (\Exception $e) {
            Log::error('Error parsing slots from interface body', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'parseSlotsFromInterfaceBody',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Extract methods from TypeScript content
     *
     * @param string $content TypeScript content
     * @param string $componentName Component name
     * @return array Extracted methods
     */
    private function extractMethodsFromContent(string $content, string $componentName): array
    {
        try {
            $methods = [];

            // Look for exposed methods in DefineComponent
            if (preg_match('/\{\}\s*,\s*([^,}]+)\s*,\s*\{\}/', $content, $matches)) {
                $methodsType = trim($matches[1]);
                $methods = array_merge($methods, $this->parseMethodsFromType($methodsType));
            }

            return $methods;
        } catch (\Exception $e) {
            Log::error('Error extracting methods from content', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'extractMethodsFromContent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get known component patterns for specific components
     *
     * @param string $componentName Component name
     * @return array Known patterns
     */
    private function getKnownComponentPatterns(string $componentName): array
    {
        try {
            $patterns = [
                'inputtext' => [
                    'props' => [
                        'modelValue' => [
                            'type' => 'string',
                            'description' => 'Value of the component for v-model binding',
                            'source' => 'known'
                        ],
                        'placeholder' => [
                            'type' => 'string',
                            'description' => 'Placeholder text for the input',
                            'source' => 'known'
                        ],
                        'disabled' => [
                            'type' => 'boolean',
                            'description' => 'When present, it specifies that the component should be disabled',
                            'source' => 'known'
                        ],
                        'readonly' => [
                            'type' => 'boolean',
                            'description' => 'When present, it specifies that the component is read-only',
                            'source' => 'known'
                        ],
                        'invalid' => [
                            'type' => 'boolean',
                            'description' => 'When present, it specifies that the component should be styled as invalid',
                            'source' => 'known'
                        ],
                        'variant' => [
                            'type' => 'string',
                            'description' => 'Specifies the input variant',
                            'enum' => ['filled', 'outlined'],
                            'source' => 'known'
                        ],
                        'size' => [
                            'type' => 'string',
                            'description' => 'Size of the input field',
                            'enum' => ['small', 'large'],
                            'source' => 'known'
                        ],
                        'fluid' => [
                            'type' => 'boolean',
                            'description' => 'Spans the full width of its parent',
                            'source' => 'known'
                        ],
                        'name' => [
                            'type' => 'string',
                            'description' => 'Name attribute of the input',
                            'source' => 'known'
                        ],
                        'autocomplete' => [
                            'type' => 'string',
                            'description' => 'Used to define a string that autocompletes',
                            'source' => 'known'
                        ]
                    ],
                    'events' => [
                        'update:modelValue' => [
                            'description' => 'Callback to invoke when the value changes',
                            'source' => 'known'
                        ],
                        'blur' => [
                            'description' => 'Callback to invoke when the component loses focus',
                            'source' => 'known'
                        ],
                        'focus' => [
                            'description' => 'Callback to invoke when the component receives focus',
                            'source' => 'known'
                        ],
                        'input' => [
                            'description' => 'Callback to invoke when the value changes',
                            'source' => 'known'
                        ],
                        'keydown' => [
                            'description' => 'Callback to invoke when a key is pressed',
                            'source' => 'known'
                        ],
                        'keyup' => [
                            'description' => 'Callback to invoke when a key is released',
                            'source' => 'known'
                        ]
                    ],
                    'methods' => [
                        'focus' => [
                            'description' => 'Focuses the input element',
                            'source' => 'known'
                        ]
                    ]
                ]
            ];

            return $patterns[$componentName] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getting known component patterns', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'getKnownComponentPatterns',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    // Helper methods...

    private function hasComponentFiles(string $directory): bool
    {
        try {
            $componentName = basename($directory);
            $expectedFiles = [
                "{$directory}/index.d.ts",
                "{$directory}/" . ucfirst($componentName) . ".vue",
                "{$directory}/index.mjs"
            ];

            foreach ($expectedFiles as $file) {
                if (File::exists($file)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function convertTypeScriptType(string $tsType): string
    {
        $typeMap = [
            'string' => 'string',
            'number' => 'number',
            'boolean' => 'boolean',
            'Date' => 'string',
            'object' => 'object',
            'any' => 'string',
            'unknown' => 'string',
            'void' => 'function',
            'HintedString' => 'string',
            'Nullable' => 'string'
        ];

        $cleanType = trim($tsType);
        $cleanType = preg_replace('/<[^>]*>/', '', $cleanType);
        $cleanType = trim($cleanType);

        if (str_contains($cleanType, '|')) {
            $types = explode('|', $cleanType);
            $cleanType = trim($types[0]);
        }

        return $typeMap[$cleanType] ?? 'string';
    }

    private function isEnumType(string $type): bool
    {
        return str_contains($type, "'") || str_contains($type, '"') || str_contains($type, '|');
    }

    private function extractEnumValues(string $typeString): array
    {
        $values = [];
        preg_match_all('/[\'"]([^\'"]+)[\'"]/', $typeString, $matches);

        if (!empty($matches[1])) {
            $values = $matches[1];
        }

        return $values;
    }

    private function mergeComponentData(array $existing, array $new): array
    {
        foreach (['props', 'events', 'methods', 'slots'] as $section) {
            $existing[$section] = array_merge($existing[$section] ?? [], $new[$section] ?? []);
        }
        return $existing;
    }

    private function addCommonPatterns(array $rawData, string $componentName): array
    {
        $accessibilityProps = [
            'accesskey' => [
                'type' => 'string',
                'description' => 'Keyboard shortcut key',
                'source' => 'common'
            ],
            'tabindex' => [
                'type' => 'number',
                'description' => 'Tab order index',
                'source' => 'common'
            ],
            'ariaLabel' => [
                'type' => 'string',
                'description' => 'ARIA label for accessibility',
                'source' => 'common'
            ],
            'ariaLabelledby' => [
                'type' => 'string',
                'description' => 'ARIA labelledby reference',
                'source' => 'common'
            ],
            'ariaDescribedby' => [
                'type' => 'string',
                'description' => 'ARIA describedby reference',
                'source' => 'common'
            ]
        ];

        $universalProps = [
            'class' => [
                'type' => 'string',
                'description' => 'CSS class names',
                'source' => 'common'
            ],
            'style' => [
                'type' => 'object',
                'description' => 'Inline styles',
                'source' => 'common'
            ],
            'pt' => [
                'type' => 'object',
                'description' => 'PrimeVue PassThrough API',
                'source' => 'common'
            ],
            'ptOptions' => [
                'type' => 'object',
                'description' => 'PassThrough options',
                'source' => 'common'
            ],
            'unstyled' => [
                'type' => 'boolean',
                'description' => 'Remove default styling',
                'source' => 'common'
            ]
        ];

        $rawData['props'] = array_merge(
            $rawData['props'] ?? [],
            $accessibilityProps,
            $universalProps
        );

        return $rawData;
    }

    // Placeholder methods for compatibility
    private function resolveExtendedProps(string $content, string $extendsFrom): array
    {
        return [];
    }
    private function parseEventsFromType(string $type): array
    {
        return [];
    }
    private function parseMethodsFromType(string $type): array
    {
        return [];
    }
    private function parseVueComponent(string $filePath, string $componentName): array
    {
        return [];
    }

    /**
     * Generate curation file from raw component data
     */
    public function generateCurationFile(string $componentName, array $rawData): string
    {
        try {
            $curationPath = storage_path("app/apex/curation/{$componentName}.json");
            File::ensureDirectoryExists(dirname($curationPath));

            $curationData = [
                '_metadata' => [
                    'component' => $componentName,
                    'scanned_at' => now()->toISOString(),
                    'instructions' => [
                        'Assign each property/event to an edition: core, pro, or enterprise',
                        'Add descriptions where missing',
                        'Remove properties not needed in Apex',
                        'Mark deprecated properties with "deprecated": true'
                    ]
                ],
                '_editions' => [
                    'core' => 'Basic functionality - free tier',
                    'pro' => 'Enhanced features - paid tier',
                    'enterprise' => 'Advanced features - enterprise tier'
                ]
            ];

            foreach (['props', 'events', 'methods', 'slots'] as $section) {
                $curationData[$section] = [];

                foreach ($rawData[$section] ?? [] as $name => $config) {
                    $curationData[$section][$name] = [
                        'type' => $config['type'] ?? 'string',
                        'description' => $config['description'] ?? "TODO: Add description for {$name}",
                        'required' => $config['required'] ?? false,
                        'edition' => 'unassigned',
                        'deprecated' => false,
                        'exclude' => false,
                        'notes' => '',
                        'source' => $config['source'] ?? 'unknown'
                    ];

                    if (isset($config['enum'])) {
                        $curationData[$section][$name]['enum'] = $config['enum'];
                    }
                }
            }

            File::put($curationPath, json_encode($curationData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return $curationPath;
        } catch (\Exception $e) {
            Log::error('Error generating curation file', [
                'folder' => 'app/Apex/PrimeReg/Services',
                'file' => 'ComponentScanner.php',
                'method' => 'generateCurationFile',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
