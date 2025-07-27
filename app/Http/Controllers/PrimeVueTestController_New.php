<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Apex\Core\Widget\WidgetRenderer;
use App\Http\Controllers\Traits\PrimeVueTest\BreadcrumbTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\KnobTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DatePickerTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableBasicTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableAdvancedTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableRowFeaturesTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableColumnFeaturesTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableGroupingTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableDataTypesTestTrait;
use App\Http\Controllers\Traits\PrimeVueTest\DataTableReorderTestTrait;
use Inertia\Inertia;

class PrimeVueTestController_New extends Controller
{
    use BreadcrumbTestTrait;
    use KnobTestTrait;
    use DatePickerTestTrait;
    use DataTableBasicTestTrait;
    use DataTableAdvancedTestTrait;
    use DataTableRowFeaturesTestTrait;
    use DataTableColumnFeaturesTestTrait;
    use DataTableGroupingTestTrait;
    use DataTableDataTypesTestTrait;
    use DataTableReorderTestTrait;

    public function index()
    {

        $widgetRenderer = app(WidgetRenderer::class);

        $widgets = array_merge(
            $this->getBreadcrumbWidgets(),
            $this->getKnobWidgets(),
            $this->getDatePickerWidgets(),
            $this->getBasicDataTableWidgets(),
            $this->getAdvancedDataTableWidgets(),
            $this->getRowFeaturesDataTableWidgets(),
            $this->getColumnFeaturesDataTableWidgets(),
            $this->getGroupingDataTableWidgets(),
            $this->getDataTypesDataTableWidgets(),
            $this->getReorderDataTableWidgets()
        );

        $renderedWidgets = $widgetRenderer->renderMany($widgets);

        return Inertia::render('PrimeVueTest_New', [
            'widgets' => $renderedWidgets
        ]);
    }
}
