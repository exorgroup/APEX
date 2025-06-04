<?php

namespace App\Http\Controllers;

use App\Apex\Core\Widget\WidgetRenderer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApexTestController2 extends Controller
{
    protected WidgetRenderer $widgetRenderer;

    public function __construct(WidgetRenderer $widgetRenderer)
    {
        $this->widgetRenderer = $widgetRenderer;
    }

    public function index()
    {
        // Define widgets using JSON-like array structure
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
            [
                'type' => 'breadcrumb',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'pi pi-chart-bar', 'url' => '/dashboard'],
                    ['label' => 'Settings', 'icon' => 'pi pi-cog', 'url' => '/settings'],
                    ['label' => 'Profile'],
                ],
            ],
        ];

        // Render widgets
        $widgets = $this->widgetRenderer->renderMany($widgetConfigs);

        return Inertia::render('ApexAxios', [
            'widgets' => $widgets,
            'rawConfig' => $widgetConfigs, // For debugging
        ]);
    }

    public function dynamicTest(Request $request)
    {
        // Accept JSON configuration from request
        $widgetConfig = $request->validate([
            'widgets' => 'required|array',
            'widgets.*.type' => 'required|string',
            'widgets.*.items' => 'sometimes|array',
        ]);

        $widgets = $this->widgetRenderer->renderMany($widgetConfig['widgets']);

        return response()->json([
            'widgets' => $widgets,
        ]);
    }
}
