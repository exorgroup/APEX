<?php

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class InputTextWidget extends BaseWidget
{
    /**
     * Get the widget type identifier
     */
    public function getType(): string
    {
        return 'inputtext';
    }

    /**
     * Get the JSON schema for validation
     */
    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['type'],
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'enum' => ['inputtext']
                ],
                'id' => [
                    'type' => 'string'
                ],
                'label' => [
                    'type' => 'string'
                ],
                'placeholder' => [
                    'type' => 'string'
                ],
                'value' => [
                    'type' => 'string'
                ],
                'icon' => [
                    'type' => 'string'
                ],
                'iconPosition' => [
                    'type' => 'string',
                    'enum' => ['left', 'right']
                ],
                'disabled' => [
                    'type' => 'boolean'
                ],
                'required' => [
                    'type' => 'boolean'
                ],
                'readonly' => [
                    'type' => 'boolean'
                ],
                'size' => [
                    'type' => 'string',
                    'enum' => ['small', 'medium', 'large']
                ],
                'feedback' => [
                    'type' => 'boolean'
                ],
                'invalidMessage' => [
                    'type' => 'string'
                ],
                'helpText' => [
                    'type' => 'string'
                ]
            ]
        ];
    }

    /**
     * Transform configuration to widget props
     */
    public function transform(array $config): array
    {
        // Extract only the allowed properties from the configuration
        $props = array_intersect_key($config, array_flip([
            'label',
            'placeholder',
            'value',
            'icon',
            'iconPosition',
            'disabled',
            'required',
            'readonly',
            'size',
            'feedback',
            'invalidMessage',
            'helpText'
        ]));

        return $props;
    }
}
