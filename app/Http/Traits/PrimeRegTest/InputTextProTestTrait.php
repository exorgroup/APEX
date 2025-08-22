<?php

/**
 * File location: app/Http/Traits/PrimeRegTest/InputTextProTestTrait.php
 * URL: /primereg-pro-test/
 * Description: Trait providing InputText Pro edition widget configurations for testing - includes all core features plus pro-specific capabilities
 */

namespace App\Http\Traits\PrimeRegTest;

use Illuminate\Support\Facades\Log;

trait InputTextProTestTrait
{
    /**
     * Get InputText Pro edition widgets array for WidgetRenderer
     * Tests core feature inheritance plus pro-specific capabilities
     *
     * @return array
     */
    public function getInputTextProWidgets(): array
    {
        try {
            return [
                // CORE FEATURES (inherited by Pro edition) - First 10 widgets

                // Basic Input - Core feature test in Pro edition
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'basic-input-pro',
                    'label' => 'Basic Text Input (Press ALT-T - Pro Edition)',
                    'placeholder' => 'Enter some text...',
                    'accesskey' => 't',
                    'value' => ''
                ],

                // Required Input - Core validation in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'required-input-pro',
                    'label' => 'Required Field (Pro Edition)',
                    'placeholder' => 'This field is required',
                    'value' => '',
                    'required' => true,
                    'invalidMessage' => 'This field is required'
                ],

                // Disabled Input - Core state in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'disabled-input-pro',
                    'label' => 'Disabled Input (Pro Edition)',
                    'placeholder' => 'Cannot edit this',
                    'value' => 'Disabled value',
                    'disabled' => true
                ],

                // Readonly Input - Core state in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'readonly-input-pro',
                    'label' => 'Readonly Input (Pro Edition)',
                    'value' => 'Read-only value',
                    'readonly' => true
                ],

                // Input with Left Icon - Core feature in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'icon-left-input-pro',
                    'label' => 'Search Input (Pro Edition)',
                    'placeholder' => 'Search...',
                    'value' => '',
                    'icon' => 'pi-search',
                    'iconPosition' => 'left'
                ],

                // Input with Right Icon - Core feature in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'icon-right-input-pro',
                    'label' => 'Email Input (Pro Edition)',
                    'placeholder' => 'Enter email...',
                    'value' => '',
                    'icon' => 'pi-envelope',
                    'iconPosition' => 'right'
                ],

                // Small Size Input - Core sizing in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'small-input-pro',
                    'label' => 'Small Input (Pro Edition)',
                    'placeholder' => 'Small size',
                    'value' => '',
                    'size' => 'small'
                ],

                // Large Size Input - Core sizing in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'large-input-pro',
                    'label' => 'Large Input (Pro Edition)',
                    'placeholder' => 'Large size',
                    'value' => '',
                    'size' => 'large'
                ],

                // Input with Help Text - Core help in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'help-input-pro',
                    'label' => 'Email Address (Pro Edition)',
                    'placeholder' => 'Enter your email',
                    'value' => '',
                    'helpText' => 'We will never share your email with anyone else.'
                ],

                // Input with Validation Feedback - Core validation in Pro
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'feedback-input-pro',
                    'label' => 'Username (Pro Edition - HERE vue event test)',
                    'placeholder' => 'Enter username',
                    'value' => 'invalid@user',
                    'feedback' => true,
                    'invalidMessage' => 'Username contains invalid characters',
                    'events' => [
                        'blur' => "edfdf",
                        'dblclick' => [
                            'type' => 'vue',
                            'handler' => 'highlightField',
                            'params' => ['{{widget:this.value}}', '{{static:fieldType}}']
                        ]
                    ]
                ],

                // PRO-SPECIFIC FEATURES - Additional widgets testing pro capabilities

                // Pro Event Handling - blur, focus events
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'event-test-input',
                    'label' => 'Event Test Input (Pro Edition)',
                    'placeholder' => 'Focus and blur to test events',
                    'value' => '',
                    'debounce' => 500, // 500ms debounce

                    'events' => [
                        'blur' => "edfdf",
                        'focus' => "tyut",
                        'dblclick' => [
                            'type' => 'server',
                            'server' => '/check-values',
                            'handler' => 'validateValues',
                            'params' => [
                                '{{widget:this.value}}',           // Current widget value
                                '{{widget:feedback-input-pro.value}}', // Other widget value
                                '{{widget:help-input-pro.value}}', // Other widget value
                                '{{static:maxLength}}',            // Static value
                            ],
                            'response' => [
                                'success' => [
                                    'type' => 'modal',
                                    'title' => 'Validation Success',
                                    'buttonText' => 'Continue',
                                    'buttonSeverity' => 'success'
                                ],

                                'error' => [
                                    'type' => 'modal',
                                    'title' => 'Validation Error',
                                    'buttonText' => 'Try Again',
                                    'buttonSeverity' => 'error',
                                    'image' => 'https://static.vecteezy.com/system/resources/thumbnails/028/149/207/small/3d-warning-or-danger-risk-message-alert-problem-icon-png.png'
                                ]

                            ]
                        ],
                    ]
                ],



                // Pro Advanced Validation
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'advanced-validation-pro',
                    'label' => 'Advanced Validation (Pro)',
                    'placeholder' => 'Enter email for real-time validation',
                    'value' => '',

                    'events' => [
                        'blur' => "edfdf",
                        'focus' => "tyut",
                        'dblclick' => [
                            'type' => 'server',
                            'server' => '/check-values',
                            'handler' => 'validateValues',
                            'params' => [
                                '{{widget:this.value}}',           // Current widget value
                                '{{widget:feedback-input-pro.value}}', // Other widget value
                                '{{widget:help-input-pro.value}}', // Other widget value
                                '{{static:maxLength}}',            // Static value
                            ],
                            'response' => [
                                'success' => ['type' => 'toast', 'severity' => 'warn'],
                                'error' => ['type' => 'toast', 'severity' => 'error']
                            ]
                        ],
                    ],
                    'advancedValidation' => [
                        'realTimeValidation' => true,
                        'customRules' => ['email', 'unique'],
                        'businessRules' => [
                            'domain' => ['allowed' => ['company.com', 'partner.com']]
                        ]
                    ]
                ],

                // Pro State Management
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'state-management-pro',
                    'label' => 'State Management (Pro)',
                    'placeholder' => 'Changes sync to server',
                    'value' => '',
                    'stateConfig' => [
                        'syncToServer' => true,
                        'localState' => true,
                        'conflictResolution' => 'merge'
                    ]
                ],

                // Pro Error Handling
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'error-handling-pro',
                    'label' => 'Pro Error Display',
                    'placeholder' => 'Errors show as toast',
                    'value' => 'invalid-value',
                    'errorConfig' => [
                        'displayType' => 'toast',
                        'position' => 'top',
                        'timeout' => 5000
                    ],
                    'invalidMessage' => 'This value triggers a toast error message'
                ],

                // Pro Parameter Injection
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'parameter-injection-pro',
                    'label' => 'Parameter Injection (Pro)',
                    'placeholder' => 'Uses context templates',
                    'value' => '',
                    'parameterConfig' => [
                        'contexts' => ['user', 'company', 'session'],
                        'templates' => [
                            'placeholder' => 'Enter {{user.name}} for {{company.name}}'
                        ],
                        'validation' => [
                            'required' => '{{user.role}} === "admin"'
                        ]
                    ]
                ],

                // Pro Clipboard Events
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'clipboard-events-pro with access',
                    'label' => 'Clipboard Events (Pro)',
                    'placeholder' => 'Try copy/cut/paste operations',
                    'value' => 'Sample text for clipboard testing',
                    'events' => [
                        'onCopy' => 'console.log("Pro copy event triggered")',
                        'onCut' => 'console.log("Pro cut event triggered")',
                        'onPaste' => 'console.log("Pro paste event triggered")'
                    ]
                ],

                // Pro Loading States
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'loading-states-pro',
                    'label' => 'Loading States (Pro)',
                    'placeholder' => 'Shows loading spinner',
                    'value' => '',
                    'serverConfig' => [
                        'endpoints' => [
                            'validate' => '/api/validate-input',
                            'save' => '/api/save-input'
                        ],
                        'timeout' => 30000,
                        'retries' => 3
                    ],
                    'events' => [
                        'onProgress' => 'console.log("Pro progress event triggered")',
                        'onAbort' => 'console.log("Pro abort event triggered")'
                    ]
                ],

                // Pro Resize and Scroll Events
                [
                    'type' => 'inputtext',
                    'edition' => 'pro',
                    'id' => 'resize-scroll-pro',
                    'label' => 'Resize/Scroll Events (Pro)',
                    'placeholder' => 'Responds to resize/scroll',
                    'value' => '',
                    'events' => [
                        'onResize' => 'console.log("Pro resize event triggered")',
                        'onScroll' => 'console.log("Pro scroll event triggered")'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in InputTextProTestTrait getInputTextProWidgets method', [
                'file' => 'app/Http/Traits/PrimeRegTest/InputTextProTestTrait.php',
                'method' => 'getInputTextProWidgets',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty array on error
            return [];
        }
    }
}
