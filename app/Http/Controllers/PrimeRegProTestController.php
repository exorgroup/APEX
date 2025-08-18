<?php

/**
 * File location: app/Http/Controllers/PrimeRegTestController.php
 * Description: Simple controller for testing APEX Core widgets using WidgetRenderer pattern
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\PrimeRegTest\InputTextProTestTrait;
use App\Apex\Core\Widget\WidgetRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PrimeRegProTestController extends Controller
{
    use InputTextProTestTrait;

    /**
     * Display the PrimeReg test page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        try {
            $widgetRenderer = app(WidgetRenderer::class);

            $widgets = [
                // InputText widgets from trait
                ...$this->getInputTextProWidgets(),

                // Future widget traits can be added here:
                // ...$this->getTextareaWidgets(),
                // ...$this->getSelectWidgets(),
                // ...$this->getDataTableWidgets(),
            ];

            $renderedWidgets = $widgetRenderer->renderMany($widgets);

            return Inertia::render('PrimeRegProTest', [
                'widgets' => $renderedWidgets,
                'pageTitle' => 'APEX Pro Widgets Test - PrimeReg',
                'pageDescription' => 'Testing APEX Pro widget implementations using WidgetRenderer'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PrimeRegTestController index method', [
                'file' => 'app/Http/Controllers/PrimeRegProTestController.php',
                'method' => 'index',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error page with empty widgets
            return Inertia::render('PrimeRegProTest', [
                'widgets' => [],
                'error' => 'Failed to load test widgets',
                'pageTitle' => 'APEX Core Widgets Test - Error'
            ]);
        }
    }
}
