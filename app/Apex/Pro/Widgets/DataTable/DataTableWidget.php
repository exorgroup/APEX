<?php
// app/Apex/Pro/Widgets/DataTable/DataTableWidget.php

namespace App\Apex\Pro\Widgets\DataTable;

use App\Apex\Core\Widgets\DataTable\DataTableWidget as CoreDataTableWidget;
use App\Apex\Pro\Widgets\DataTable\Traits\HasDataFormatting;
use App\Apex\Pro\Widgets\DataTable\Traits\HasSize;
use App\Apex\Pro\Widgets\DataTable\Traits\HasMultipleColumnsSort;
use App\Apex\Pro\Widgets\DataTable\Traits\HasPresort;
use App\Apex\Pro\Widgets\DataTable\Traits\HasMultipleRowSelection;
use App\Apex\Pro\Widgets\DataTable\Traits\HasRadioCheckboxSelection;
use App\Apex\Pro\Widgets\DataTable\Traits\HasConditionalStyle;
use App\Apex\Pro\Widgets\DataTable\Traits\HasCrudActions;
use App\Apex\Pro\Widgets\DataTable\Composables\UseBasicFilter;
use App\Apex\Pro\Widgets\DataTable\Composables\UseFrozenRows;
use App\Apex\Pro\Widgets\DataTable\Composables\UseFrozenColumns;
use App\Apex\Pro\Widgets\DataTable\Composables\UsePreload;
use App\Apex\Pro\Widgets\DataTable\Composables\UseColumnGroup;
use App\Apex\Pro\Widgets\DataTable\Composables\UseRowGroup;
use App\Apex\Pro\Widgets\DataTable\Composables\UseColumnToggle;
use App\Apex\Pro\Widgets\DataTable\Composables\UseReorder;
use App\Apex\Pro\Widgets\DataTable\Composables\UseGroupActions;

class DataTableWidget extends CoreDataTableWidget
{
    use HasDataFormatting;
    use HasSize;
    use HasMultipleColumnsSort;
    use HasPresort;
    use HasMultipleRowSelection;
    use HasRadioCheckboxSelection;
    use HasConditionalStyle;
    use HasCrudActions;
    use UseBasicFilter;
    use UseFrozenRows;
    use UseFrozenColumns;
    use UsePreload;
    use UseColumnGroup;
    use UseRowGroup;
    use UseColumnToggle;
    use UseReorder;
    use UseGroupActions;

    public function getSchema(): array
    {
        return array_merge(
            parent::getSchema(),
            $this->getDataFormattingSchema(),
            $this->getSizeSchema(),
            $this->getMultipleColumnsSortSchema(),
            $this->getPresortSchema(),
            $this->getMultipleRowSelectionSchema(),
            $this->getRadioCheckboxSelectionSchema(),
            $this->getConditionalStyleSchema(),
            $this->getCrudActionsSchema(),
            $this->getBasicFilterSchema(),
            $this->getFrozenRowsSchema(),
            $this->getFrozenColumnsSchema(),
            $this->getPreloadSchema(),
            $this->getColumnGroupSchema(),
            $this->getRowGroupSchema(),
            $this->getColumnToggleSchema(),
            $this->getReorderSchema(),
            $this->getGroupActionsSchema()
        );
    }

    public function transform(array $config): array
    {
        $baseTransform = parent::transform($config);

        // Pro-level column transformations
        $processedColumns = $this->transformDataFormattingColumns($baseTransform['props']['columns']);

        return array_merge($baseTransform, [
            'props' => array_merge($baseTransform['props'], [
                'columns' => $processedColumns,
                ...$this->transformDataFormatting($config),
                ...$this->transformSize($config),
                ...$this->transformMultipleColumnsSort($config),
                ...$this->transformPresort($config),
                ...$this->transformMultipleRowSelection($config),
                ...$this->transformRadioCheckboxSelection($config),
                ...$this->transformConditionalStyle($config),
                ...$this->transformCrudActions($config),
                ...$this->transformBasicFilter($config),
                ...$this->transformFrozenRows($config),
                ...$this->transformFrozenColumns($config),
                ...$this->transformPreload($config),
                ...$this->transformColumnGroup($config),
                ...$this->transformRowGroup($config),
                ...$this->transformColumnToggle($config),
                ...$this->transformReorder($config),
                ...$this->transformGroupActions($config)
            ])
        ]);
    }
}
