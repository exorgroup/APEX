<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: PRO InputText widget extending Core InputText with event handling, parameter injection, state management and advanced validation capabilities
 * File location: app/Apex/Pro/Widgets/Forms/InputText/InputTextWidget.php
 */

namespace App\Apex\Pro\Widgets\Forms\InputText;

use App\Apex\Core\Widgets\Forms\InputText\InputTextWidget as CoreInputTextWidget;
use App\Apex\Pro\Widget\PrimeVueBaseWidget\PrimeVueBaseWidget;
use Illuminate\Support\Facades\Log;

class InputTextWidget extends CoreInputTextWidget
{
    use PrimeVueBaseWidget;

    /**
     * Constructor with PRO feature initialization
     * @param array $config Widget configuration array
     */
    public function __construct(array $config = [])
    {
        try {
            // Initialize core widget first
            parent::__construct($config);

            // Initialize PRO features if licensed
            if ($this->hasProLicense()) {
                $this->initializeProFeatures($config);
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
     * Get complete widget schema including PRO features
     * @return array Complete widget schema with PRO extensions
     */
    public function getSchema(): array
    {
        try {
            // Get core schema first
            $coreSchema = parent::getSchema();

            // If PRO license available, add PRO features
            if ($this->hasProLicense()) {
                // Add PRO event handling
                $coreSchema['properties']['events'] = $this->getEventSchema();

                // Add PRO state management
                $coreSchema['properties']['stateConfig'] = $this->getStateSchema();

                // Add PRO error handling
                $coreSchema['properties']['errorConfig'] = $this->getErrorSchema();

                // Add PRO parameter injection
                $coreSchema['properties']['parameterConfig'] = $this->getParameterSchema();

                // Add PRO validation features
                $coreSchema['properties']['advancedValidation'] = $this->getAdvancedValidationSchema();

                // Add PRO server communication
                $coreSchema['properties']['serverConfig'] = $this->getServerConfigSchema();
            }

            return $coreSchema;
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
     * Transform configuration with PRO features
     * @param array $config Widget configuration array
     * @return array Transformed configuration with PRO features
     */
    public function transform(array $config): array
    {
        try {
            // Get core transformation first
            $transformed = parent::transform($config);

            // Add PRO transformations if licensed
            if ($this->hasProLicense()) {
                $transformed = $this->transformPro($config);
            }

            return $transformed;
        } catch (\Exception $e) {
            Log::error('Error transforming PRO configuration', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'transform',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return parent::transform($config);
        }
    }

    /**
     * Validate email format with business rules (widget-specific validation)
     * @param string $email Email address to validate
     * @return array Validation result with status and message
     */
    protected function validateEmailFormat(string $email): array
    {
        try {
            // Basic email format validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'valid' => false,
                    'message' => 'Invalid email format',
                    'field' => 'email',
                    'code' => 'INVALID_FORMAT'
                ];
            }

            // Business rule: Check for valid domain
            $domain = substr(strrchr($email, "@"), 1);
            if (empty($domain)) {
                return [
                    'valid' => false,
                    'message' => 'Email domain is required',
                    'field' => 'email',
                    'code' => 'MISSING_DOMAIN'
                ];
            }

            // Business rule: Block disposable email providers (example)
            $disposableDomains = ['tempmail.com', '10minutemail.com', 'guerrillamail.com'];
            if (in_array($domain, $disposableDomains)) {
                return [
                    'valid' => false,
                    'message' => 'Disposable email addresses are not allowed',
                    'field' => 'email',
                    'code' => 'DISPOSABLE_EMAIL'
                ];
            }

            return [
                'valid' => true,
                'message' => 'Email format is valid',
                'field' => 'email',
                'code' => 'VALID'
            ];
        } catch (\Exception $e) {
            Log::error('Error validating email format', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'validateEmailFormat',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'valid' => false,
                'message' => 'Validation error occurred',
                'field' => 'email',
                'code' => 'VALIDATION_ERROR'
            ];
        }
    }

    /**
     * Format phone number for display (widget-specific formatting)
     * @param string $phone Phone number to format
     * @return string Formatted phone number
     */
    protected function formatPhoneNumber(string $phone): string
    {
        try {
            // Remove all non-numeric characters
            $cleaned = preg_replace('/[^0-9]/', '', $phone);

            // Format based on length
            switch (strlen($cleaned)) {
                case 10:
                    // US format: (123) 456-7890
                    return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $cleaned);
                case 11:
                    // US format with country code: +1 (123) 456-7890
                    return preg_replace('/(\d{1})(\d{3})(\d{3})(\d{4})/', '+$1 ($2) $3-$4', $cleaned);
                default:
                    // Return as-is if format not recognized
                    return $phone;
            }
        } catch (\Exception $e) {
            Log::error('Error formatting phone number', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'formatPhoneNumber',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $phone;
        }
    }

    /**
     * Validate credit card number using Luhn algorithm (widget-specific validation)
     * @param string $cardNumber Credit card number to validate
     * @return array Validation result with card type and status
     */
    protected function validateCreditCard(string $cardNumber): array
    {
        try {
            // Remove spaces and dashes
            $cleaned = preg_replace('/[\s\-]/', '', $cardNumber);

            // Check if all digits
            if (!ctype_digit($cleaned)) {
                return [
                    'valid' => false,
                    'message' => 'Credit card number must contain only digits',
                    'cardType' => null
                ];
            }

            // Luhn algorithm validation
            $sum = 0;
            $alternate = false;

            for ($i = strlen($cleaned) - 1; $i >= 0; $i--) {
                $n = intval($cleaned[$i]);

                if ($alternate) {
                    $n *= 2;
                    if ($n > 9) {
                        $n = ($n % 10) + 1;
                    }
                }

                $sum += $n;
                $alternate = !$alternate;
            }

            $isValid = ($sum % 10 === 0);
            $cardType = $this->detectCardType($cleaned);

            return [
                'valid' => $isValid,
                'message' => $isValid ? 'Valid credit card number' : 'Invalid credit card number',
                'cardType' => $cardType,
                'maskedNumber' => $this->maskCardNumber($cleaned)
            ];
        } catch (\Exception $e) {
            Log::error('Error validating credit card', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'validateCreditCard',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'valid' => false,
                'message' => 'Validation error occurred',
                'cardType' => null
            ];
        }
    }

    /**
     * Detect credit card type from number (helper method)
     * @param string $cardNumber Clean credit card number
     * @return string|null Card type or null if unknown
     */
    protected function detectCardType(string $cardNumber): ?string
    {
        try {
            $patterns = [
                'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
                'mastercard' => '/^5[1-5][0-9]{14}$/',
                'amex' => '/^3[47][0-9]{13}$/',
                'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/'
            ];

            foreach ($patterns as $type => $pattern) {
                if (preg_match($pattern, $cardNumber)) {
                    return $type;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error detecting card type', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'detectCardType',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Mask credit card number for security (helper method)
     * @param string $cardNumber Clean credit card number
     * @return string Masked card number
     */
    protected function maskCardNumber(string $cardNumber): string
    {
        try {
            $length = strlen($cardNumber);
            if ($length < 4) {
                return str_repeat('*', $length);
            }

            // Show first 4 and last 4 digits
            $masked = substr($cardNumber, 0, 4) . str_repeat('*', $length - 8) . substr($cardNumber, -4);
            return $masked;
        } catch (\Exception $e) {
            Log::error('Error masking card number', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'maskCardNumber',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return str_repeat('*', strlen($cardNumber));
        }
    }

    /**
     * Get advanced validation schema for PRO features
     * @return array Advanced validation schema
     */
    protected function getAdvancedValidationSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Advanced validation configuration',
                'properties' => [
                    'realTimeValidation' => [
                        'type' => 'boolean',
                        'description' => 'Enable real-time validation',
                        'default' => true
                    ],
                    'customRules' => [
                        'type' => 'array',
                        'description' => 'Custom validation rules',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'rule' => ['type' => 'string'],
                                'message' => ['type' => 'string'],
                                'serverValidation' => ['type' => 'boolean']
                            ]
                        ]
                    ],
                    'businessRules' => [
                        'type' => 'object',
                        'description' => 'Business-specific validation rules'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting advanced validation schema', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getAdvancedValidationSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get server configuration schema
     * @return array Server configuration schema
     */
    protected function getServerConfigSchema(): array
    {
        try {
            return [
                'type' => 'object',
                'description' => 'Server communication configuration',
                'properties' => [
                    'endpoints' => [
                        'type' => 'object',
                        'description' => 'Server endpoint configurations',
                        'properties' => [
                            'validate' => ['type' => 'string'],
                            'autocomplete' => ['type' => 'string'],
                            'format' => ['type' => 'string']
                        ]
                    ],
                    'timeout' => [
                        'type' => 'integer',
                        'description' => 'Request timeout in milliseconds',
                        'default' => 5000
                    ],
                    'retries' => [
                        'type' => 'integer',
                        'description' => 'Number of retry attempts',
                        'default' => 3
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting server config schema', [
                'folder' => 'app/Apex/Pro/Widgets/Forms/InputText',
                'file' => 'InputTextWidget.php',
                'method' => 'getServerConfigSchema',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
