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

    // app/Http/Controllers/PrimeVueTestController.php - Updated index method
    public function index()
    {
        $widgetRenderer = app(WidgetRenderer::class);

        $widgets = [
            // Existing breadcrumb widget
            [
                'type' => 'breadcrumb',
                'items' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Components', 'url' => '/components'],
                    ['label' => 'PrimeVue Test'],
                ],
                'home' => ['icon' => 'pi pi-home', 'url' => '/']
            ],

            // Existing knob widgets...
            [
                'type' => 'knob',
                'value' => 20,
                'min' => 0,
                'max' => 40,
                'step' => 1,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#ef4444',
                'rangeColor' => '#fecaca',
                'valueTemplate' => '{value}°C',
                'label' => 'Temperature',
                'readonly' => false
            ],
            [
                'type' => 'knob',
                'value' => 50,
                'min' => 0,
                'max' => 100,
                'step' => 10,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#3b82f6',
                'rangeColor' => '#dbeafe',
                'valueTemplate' => '{value}%',
                'label' => 'Volume',
                'readonly' => false
            ],
            [
                'type' => 'knob',
                'value' => 75,
                'min' => 0,
                'max' => 100,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#10b981',
                'rangeColor' => '#d1fae5',
                'valueTemplate' => '{value}%',
                'label' => 'Progress',
                'readonly' => true
            ],

            // Existing datepicker widgets...
            [
                'type' => 'datepicker',
                'placeholder' => 'Select a date',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
                'showButtonBar' => true,
                'label' => 'Basic Date'
            ],
            [
                'type' => 'datepicker',
                'placeholder' => 'Select date range',
                'selectionMode' => 'range',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
                'label' => 'Date Range'
            ],
            [
                'type' => 'datepicker',
                'placeholder' => 'Pick multiple dates',
                'selectionMode' => 'multiple',
                'dateFormat' => 'mm/dd/yy',
                'showIcon' => true,
                'label' => 'Multiple Dates'
            ],

            // NEW: DataTable widget with server-side data
            [
                'type' => 'datatable',
                'columns' => [
                    ['field' => 'code', 'header' => 'Code', 'sortable' => true],
                    ['field' => 'name', 'header' => 'Name', 'sortable' => true, 'filter' => true],
                    ['field' => 'category', 'header' => 'Category', 'sortable' => true, 'filter' => true],
                    ['field' => 'price', 'header' => 'Price', 'sortable' => true],
                    ['field' => 'quantity', 'header' => 'Quantity', 'sortable' => true],
                    ['field' => 'inventoryStatus', 'header' => 'Status', 'sortable' => true, 'filter' => true],
                    ['field' => 'rating', 'header' => 'Rating', 'sortable' => true]
                ],
                'dataSource' => [
                    'url' => '/products',
                    'method' => 'GET',
                    'lazy' => true
                ],
                'paginator' => true,
                'rows' => 10,
                'rowsPerPageOptions' => [5, 10, 25, 50],
                'globalFilter' => true,
                'stripedRows' => true,
                'selectionMode' => 'multiple',
                'exportable' => true,
                'tableStyle' => 'min-width: 60rem'
            ],

            // NEW: Simple DataTable with client-side data
            [
                'type' => 'datatable',
                'columns' => [
                    ['field' => 'code', 'header' => 'Code'],
                    ['field' => 'name', 'header' => 'Name'],
                    ['field' => 'category', 'header' => 'Category'],
                    ['field' => 'price', 'header' => 'Price']
                ],
                'dataSource' => [
                    'url' => '/products/mini',
                    'method' => 'GET',
                    'lazy' => false
                ],
                'paginator' => false,
                'stripedRows' => true,
                'tableStyle' => 'min-width: 30rem'
            ]
        ];

        $renderedWidgets = $widgetRenderer->renderMany($widgets);

        return Inertia::render('PrimeVueTest', [
            'widgets' => $renderedWidgets
        ]);
    }
}
