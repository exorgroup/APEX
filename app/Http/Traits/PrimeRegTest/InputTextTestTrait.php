<?php

/**
 * File location: app/Http/Traits/PrimeRegTest/InputTextTestTrait.php
 * Description: Simple trait providing InputText widget configurations for testing
 */

namespace App\Http\Traits\PrimeRegTest;

use Illuminate\Support\Facades\Log;

trait InputTextTestTrait
{
    /**
     * Get InputText widgets array for WidgetRenderer
     *
     * @return array
     */
    public function getInputTextWidgets(): array
    {
        try {
            return [
                // Basic Input
                [
                    'type' => 'inputtext',
                    'id' => 'basic-input',
                    'label' => 'Basic Text Input',
                    'placeholder' => 'Enter some text...',
                    'value' => ''
                ],

                // Required Input
                [
                    'type' => 'inputtext',
                    'id' => 'required-input',
                    'label' => 'Required Field',
                    'placeholder' => 'This field is required',
                    'value' => '',
                    'required' => true,
                    'invalidMessage' => 'This field is required'
                ],

                // Disabled Input
                [
                    'type' => 'inputtext',
                    'id' => 'disabled-input',
                    'label' => 'Disabled Input',
                    'placeholder' => 'Cannot edit this',
                    'value' => 'Disabled value',
                    'disabled' => true
                ],

                // Readonly Input
                [
                    'type' => 'inputtext',
                    'id' => 'readonly-input',
                    'label' => 'Readonly Input',
                    'value' => 'Read-only value',
                    'readonly' => true
                ],

                // Input with Left Icon
                [
                    'type' => 'inputtext',
                    'id' => 'icon-left-input',
                    'label' => 'Search Input',
                    'placeholder' => 'Search...',
                    'value' => '',
                    'icon' => 'pi-search',
                    'iconPosition' => 'left'
                ],

                // Input with Right Icon
                [
                    'type' => 'inputtext',
                    'id' => 'icon-right-input',
                    'label' => 'Email Input',
                    'placeholder' => 'Enter email...',
                    'value' => '',
                    'icon' => 'pi-envelope',
                    'iconPosition' => 'right'
                ],

                // Small Size Input
                [
                    'type' => 'inputtext',
                    'id' => 'small-input',
                    'label' => 'Small Input',
                    'placeholder' => 'Small size',
                    'value' => '',
                    'size' => 'small'
                ],

                // Large Size Input
                [
                    'type' => 'inputtext',
                    'id' => 'large-input',
                    'label' => 'Large Input',
                    'placeholder' => 'Large size',
                    'value' => '',
                    'size' => 'large'
                ],

                // Input with Help Text
                [
                    'type' => 'inputtext',
                    'id' => 'help-input',
                    'label' => 'Email Address',
                    'placeholder' => 'Enter your email',
                    'value' => '',
                    'helpText' => 'We will never share your email with anyone else.'
                ],

                // Input with Validation Feedback
                [
                    'type' => 'inputtext',
                    'id' => 'feedback-input',
                    'label' => 'Username',
                    'placeholder' => 'Enter username',
                    'value' => 'invalid@user',
                    'feedback' => true,
                    'invalidMessage' => 'Username contains invalid characters'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in InputTextTestTrait getInputTextWidgets method', [
                'file' => 'app/Http/Traits/PrimeRegTest/InputTextTestTrait.php',
                'method' => 'getInputTextWidgets',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty array on error
            return [];
        }
    }
}
