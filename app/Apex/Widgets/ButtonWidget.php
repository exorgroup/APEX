<?php 

namespace App\Apex\Widgets;

use App\Apex\Core\Widget\BaseWidget;

class ButtonWidget extends BaseWidget
{
    public function getType(): string
    {
        return 'button';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'Unique identifier for the button widget'
                ],
                'label' => [
                    'type' => 'string',
                    'description' => 'Button text label',
                    'required' => true
                ],
                'icon' => [
                    'type' => 'string',
                    'description' => 'PrimeIcons class for button icon'
                ],
                'iconPos' => [
                    'type' => 'string',
                    'enum' => ['left', 'right', 'top', 'bottom'],
                    'default' => 'left',
                    'description' => 'Position of the icon'
                ],
                'severity' => [
                    'type' => 'string',
                    'enum' => ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'help', 'contrast'],
                    'default' => 'primary',
                    'description' => 'Button severity/color theme'
                ],
                'size' => [
                    'type' => 'string',
                    'enum' => ['small', 'normal', 'large'],
                    'default' => 'normal',
                    'description' => 'Button size'
                ],
                'outlined' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Whether button should be outlined style'
                ],
                'rounded' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Whether button should have rounded corners'
                ],
                'text' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Whether button should be text style (no background)'
                ],
                'raised' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Whether button should have raised effect'
                ],
                'disabled' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Whether button is disabled'
                ],
                'loading' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Whether button shows loading state'
                ],
                'loadingIcon' => [
                    'type' => 'string',
                    'default' => 'pi pi-spinner pi-spin',
                    'description' => 'Icon to show when loading'
                ],
                'badge' => [
                    'type' => 'string',
                    'description' => 'Badge value to display'
                ],
                'badgeSeverity' => [
                    'type' => 'string',
                    'enum' => ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'contrast'],
                    'description' => 'Badge severity/color'
                ],
                'onClick' => [
                    'type' => 'string',
                    'description' => 'JavaScript function name or code to execute on click'
                ],
                'href' => [
                    'type' => 'string',
                    'description' => 'URL to navigate to when clicked (makes it a link button)'
                ],
                'target' => [
                    'type' => 'string',
                    'enum' => ['_self', '_blank', '_parent', '_top'],
                    'default' => '_self',
                    'description' => 'Target for href navigation'
                ]
            ]
        ];
    }

    public function transform(array $config): array
    {
        // Extract button configuration
        $buttonConfig = [
            'label' => $config['label'] ?? 'Button',
            'icon' => $config['icon'] ?? null,
            'iconPos' => $config['iconPos'] ?? 'left',
            'severity' => $config['severity'] ?? 'primary',
            'size' => $config['size'] ?? 'normal',
            'outlined' => $config['outlined'] ?? false,
            'rounded' => $config['rounded'] ?? false,
            'text' => $config['text'] ?? false,
            'raised' => $config['raised'] ?? false,
            'disabled' => $config['disabled'] ?? false,
            'loading' => $config['loading'] ?? false,
            'loadingIcon' => $config['loadingIcon'] ?? 'pi pi-spinner pi-spin',
            'badge' => $config['badge'] ?? null,
            'badgeSeverity' => $config['badgeSeverity'] ?? null,
            'onClick' => $config['onClick'] ?? null,
            'href' => $config['href'] ?? null,
            'target' => $config['target'] ?? '_self',
        ];

        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'config' => $buttonConfig,
        ];
    }
}
