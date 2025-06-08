<?php

namespace App\Http\Controllers;

use App\Apex\Core\Widget\WidgetRenderer;
use Inertia\Inertia;

class PrimeVueTestController extends Controller
{
    protected WidgetRenderer $widgetRenderer;

    public function __construct(WidgetRenderer $widgetRenderer)
    {
        $this->widgetRenderer = $widgetRenderer;
    }

    public function index()
    {
        // Define widget configurations
        $widgetConfigs = [
            // Breadcrumb widget
            [
                'type' => 'breadcrumb',
                'id' => 'main-breadcrumb',
                'items' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'APEX Framework', 'url' => '/apex'],
                    ['label' => 'Widget Test', 'disabled' => true],
                ],
                'home' => [
                    'icon' => 'pi pi-home',
                    'url' => '/',
                ],
            ],
            // Temperature knob widget
            [
                'type' => 'knob',
                'id' => 'temperature-knob',
                'value' => 22,
                'min' => 0,
                'max' => 40,
                'step' => 0.5,
                'size' => 120,
                'strokeWidth' => 8,
                'showValue' => true,
                'valueTemplate' => '{value}°C',
                'valueColor' => '#ff5757',
            ],
            // Volume knob widget
            [
                'type' => 'knob',
                'id' => 'volume-knob',
                'value' => 50,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'size' => 100,
                'strokeWidth' => 6,
                'showValue' => true,
                'valueTemplate' => '{value}%',
                'valueColor' => '#20a8d8',
            ],
            // Progress knob widget (readonly)
            [
                'type' => 'knob',
                'id' => 'progress-knob',
                'value' => 75,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'size' => 80,
                'strokeWidth' => 10,
                'showValue' => true,
                'readonly' => true,
                'valueTemplate' => '{value}%',
                'valueColor' => '#4dbd74',
                'rangeColor' => '#e0e0e0',
            ],
            // Basic date picker
            [
                'type' => 'datepicker',
                'id' => 'basic-datepicker',
                'placeholder' => 'Select a date',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
            ],
            // Date picker with time
            [
                'type' => 'datepicker',
                'id' => 'datetime-picker',
                'placeholder' => 'Select date and time',
                'dateFormat' => 'mm/dd/yy',
                'showTime' => true,
                'showIcon' => true,
                'hourFormat' => '12',
            ],
            // Inline date picker
            [
                'type' => 'datepicker',
                'id' => 'inline-datepicker',
                'inline' => true,
                'showButtonBar' => true,
            ],
            // Range date picker
            [
                'type' => 'datepicker',
                'id' => 'range-datepicker',
                'placeholder' => 'Select date range',
                'selectionMode' => 'range',
                'showIcon' => true,
                'numberOfMonths' => 2,
            ],
            // Date picker with restrictions
            [
                'type' => 'datepicker',
                'id' => 'restricted-datepicker',
                'placeholder' => 'Future dates only',
                'minDate' => date('Y-m-d'),
                'maxDate' => date('Y-m-d', strtotime('+30 days')),
                'showIcon' => true,
                'showButtonBar' => true,
            ],
            // Input text widgets
            [
                'type' => 'inputtext',
                'id' => 'basic-input',
                'label' => 'Basic Input',
                'placeholder' => 'Enter text here...',
            ],
            [
                'type' => 'inputtext',
                'id' => 'search-input',
                'label' => 'Search Input',
                'placeholder' => 'Search...',
                'icon' => 'pi-search',
                'iconPosition' => 'left',
            ],
            [
                'type' => 'inputtext',
                'id' => 'email-input',
                'label' => 'Email Address',
                'placeholder' => 'your.email@example.com',
                'icon' => 'pi-envelope',
                'iconPosition' => 'left',
                'required' => true,
                'helpText' => 'We\'ll never share your email with anyone else.',
            ],
            [
                'type' => 'inputtext',
                'id' => 'disabled-input',
                'label' => 'Disabled Input',
                'value' => 'This input is disabled',
                'disabled' => true,
            ],
            [
                'type' => 'inputtext',
                'id' => 'password-input',
                'label' => 'Password',
                'placeholder' => 'Enter your password',
                'icon' => 'pi-lock',
                'iconPosition' => 'right',
                'required' => true,
                'helpText' => 'Your password must be at least 8 characters long.',
            ],
        ];

        // Render widgets
        $widgets = $this->widgetRenderer->renderMany($widgetConfigs);

        return Inertia::render('PrimeVueTest', [
            'widgets' => $widgets,
        ]);
    }
}
