<?php
// app/Apex/Enterprise/Widgets/DataTable/DataTableWidget.php

namespace App\Apex\Enterprise\Widgets\DataTable;

use App\Apex\Pro\Widgets\DataTable\DataTableWidget as ProDataTableWidget;
use App\Apex\Enterprise\Widgets\DataTable\Traits\HasApexWidgets;
use App\Apex\Enterprise\Widgets\DataTable\Traits\HasRemovableSort;
use App\Apex\Enterprise\Widgets\DataTable\Composables\UseAdvancedFilter;
use App\Apex\Enterprise\Widgets\DataTable\Composables\UseRowExpansion;
use App\Apex\Enterprise\Widgets\DataTable\Composables\UseContextMenu;
use App\Apex\Enterprise\Widgets\DataTable\Composables\UseExport;
use App\Apex\Enterprise\Widgets\DataTable\Composables\UseCrudActions;
use App\Apex\Enterprise\Widgets\DataTable\Composables\UseGroupActions;

class DataTableWidget extends ProDataTableWidget
{
    use HasApexWidgets;
    use HasRemovableSort;
    use UseAdvancedFilter;
    use UseRowExpansion;
    use UseContextMenu;
    use UseExport;
    use UseCrudActions;
    use UseGroupActions;

    public function getSchema(): array
    {
        return array_merge(
            parent::getSchema(),
            $this->getApexWidgetsSchema(),
            $this->getRemovableSortSchema(),
            $this->getAdvancedFilterSchema(),
            $this->getRowExpansionSchema(),
            $this->getContextMenuSchema(),
            $this->getExportSchema(),
            $this->getCrudActionsSchema(),
            $this->getGroupActionsSchema()
        );
    }

    public function transform(array $config): array
    {
        $baseTransform = parent::transform($config);

        // Enterprise-level column transformations
        $processedColumns = $this->transformApexWidgetColumns($baseTransform['props']['columns']);

        return array_merge($baseTransform, [
            'props' => array_merge($baseTransform['props'], [
                'columns' => $processedColumns,
                ...$this->transformApexWidgets($config),
                ...$this->transformRemovableSort($config),
                ...$this->transformAdvancedFilter($config),
                ...$this->transformRowExpansion($config),
                ...$this->transformContextMenu($config),
                ...$this->transformExport($config),
                ...$this->transformCrudActions($config),
                ...$this->transformGroupActions($config)
            ])
        ]);
    }
}
