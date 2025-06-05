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
        // Define the breadcrumb widget configuration
        $widgetConfigs = [
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
        ];

        // Render widgets
        $widgets = $this->widgetRenderer->renderMany($widgetConfigs);

        return Inertia::render('PrimeVueTest', [
            'widgets' => $widgets,
        ]);
    }
}
