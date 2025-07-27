<!-- resources/js/components/apex/enterprise/widgets/DataTable/DataTableWidget.vue -->
<script setup lang="ts">
import { ref, onMounted, computed, defineAsyncComponent } from 'vue';
import PDataTable from 'primevue/datatable';
import PColumn from 'primevue/column';
import PColumnGroup from 'primevue/columngroup';
import PRow from 'primevue/row';
import PInputText from 'primevue/inputtext';
import PButton from 'primevue/button';
import PMultiSelect from 'primevue/multiselect';
import PContextMenu from 'primevue/contextmenu';
import PDivider from 'primevue/divider';
import WidgetRenderer from '../../../WidgetRenderer.vue';

// Core traits (inherited from Pro which inherits from Core)
import { useDynamicColumns } from '../../pro/widgets/DataTable/traits/useDynamicColumns.js';
import { useTemplate } from '../../../core/widgets/DataTable/traits/useTemplate.js';
import { useStripedRows } from '../../../core/widgets/DataTable/traits/useStripedRows.js';
import { usePagination } from '../../../core/widgets/DataTable/traits/usePagination.js';
import { usePaginationTemplate } from '../../../core/widgets/DataTable/traits/usePaginationTemplate.js';
import { useSearch } from '../../../core/widgets/DataTable/traits/useSearch.js';
import { useStateful } from '../../../core/widgets/DataTable/traits/useStateful.js';

// Pro traits (inherited)
import { useDataFormatting } from '../../pro/widgets/DataTable/traits/useDataFormatting.js';
import { useSize } from '../../pro/widgets/DataTable/traits/useSize.js';
import { useMultipleColumnsSort } from '../../pro/widgets/DataTable/traits/useMultipleColumnsSort.js';
import { usePresort } from '../../pro/widgets/DataTable/traits/usePresort.js';
import { useMultipleRowSelection } from '../../pro/widgets/DataTable/traits/useMultipleRowSelection.js';
import { useRadioCheckboxSelection } from '../../pro/widgets/DataTable/traits/useRadioCheckboxSelection.js';
import { useConditionalStyle } from '../../pro/widgets/DataTable/traits/useConditionalStyle.js';
import { useCrudActions } from '../../pro/widgets/DataTable/traits/useCrudActions.js';

// Enterprise traits
import { useApexWidgets } from './traits/useApexWidgets.js';
import { useRemovableSort } from './traits/useRemovableSort.js';

// Core composables (inherited)
import { useLazy } from '../../../core/widgets/DataTable/composables/useLazy.js';

// Pro composables (inherited)
import { useBasicFilter } from '../../pro/widgets/DataTable/composables/useBasicFilter.js';
import { useFrozenRows } from '../../pro/widgets/DataTable/composables/useFrozenRows.js';
import { useFrozenColumns } from '../../pro/widgets/DataTable/composables/useFrozenColumns.js';
import { usePreload } from '../../pro/widgets/DataTable/composables/usePreload.js';
import { useColumnGroup } from '../../pro/widgets/DataTable/composables/useColumnGroup.js';
import { useRowGroup } from '../../pro/widgets/DataTable/composables/useRowGroup.js';
import { useColumnToggle } from '../../pro/widgets/DataTable/composables/useColumnToggle.js';
import { useReorder } from '../../pro/widgets/DataTable/composables/useReorder.js';
import { useGroupActions } from '../../pro/widgets/DataTable/composables/useGroupActions.js';

// Enterprise composables
import { useAdvancedFilter } from './composables/useAdvancedFilter.js';
import { useRowExpansion } from './composables/useRowExpansion.js';
import { useContextMenu } from './composables/useContextMenu.js';
import { useExport } from './composables/useExport.js';
import { useCrudActions as useEnterpriseCrudActions } from './composables/useCrudActions.js';
import { useGroupActions as useEnterpriseGroupActions } from './composables/useGroupActions.js';

interface Column {
    field: string;
    header: string;
    sortable?: boolean;
    style?: string;
    bodyStyle?: string;
    headerStyle?: string;
    hidden?: boolean;
    resizable?: boolean;
    frozen?: boolean;
    exportable?: boolean;
    dataType?: string;
    format?: string | number;
    leadText?: string;
    trailText?: string;
    widgetConfig?: any;
    url?: string;
    urlTarget?: string;
    clickable?: boolean;
    action?: string;
    actionField?: string;
}

interface Props {
    widgetId: string;
    columns: Column[];
    dataKey?: string;
    loading?: boolean;
    emptyMessage?: string;
    tableStyle?: string;
    tableClass?: string;
    responsiveLayout?: 'scroll' | 'stack';
    staticData?: any[];
    // Pro-specific props (inherited)
    size?: 'small' | 'normal' | 'large';
    sortMode?: 'single' | 'multiple';
    selectionMode?: 'single' | 'multiple' | 'checkbox';
    conditionalStyles?: any[];
    // Enterprise-specific props
    removableSort?: boolean;
    contextMenu?: any;
    rowExpansion?: any;
    exportable?: boolean;
    exportFormats?: string[];
    exportFilename?: string;
    advancedFilter?: any;
}

const props = withDefaults(defineProps<Props>(), {
    dataKey: 'id',
    loading: false,
    emptyMessage: 'No records found',
    tableStyle: 'min-width: 50rem',
    responsiveLayout: 'scroll',
    size: 'normal',
    sortMode: 'single',
    removableSort: true,
    exportable: false,
    exportFormats: () => ['csv', 'excel', 'pdf'],
    exportFilename: 'data-export'
});

const emit = defineEmits<{
    action: [payload: { action: string; data: any; value: any }];
    headerAction: [action: string];
    'crud-action': [payload: { action: string; id: any; data: any }];
    'group-action': [payload: { action: string; items: any[] }];
    'context-menu-action': [payload: { action: string; data: any; item: any }];
    'row-expand': [event: any];
    'row-collapse': [event: any];
}>();

// Use Core traits
const { visibleColumns, initVisibleColumns, onColumnToggle } = useDynamicColumns(props);
const { headerConfig, footerConfig } = useTemplate(props);
const { stripedRowsEnabled } = useStripedRows(props);
const { paginationConfig, first, onPage } = usePagination(props);
const { paginationTemplateConfig } = usePaginationTemplate(props);
const { searchConfig, globalFilterValue, onGlobalFilter } = useSearch(props);
const { statefulConfig } = useStateful(props);

// Use Pro traits
const { formatCellValue } = useDataFormatting(props);
const { sizeConfig } = useSize(props);
const { multiSortConfig, multiSortMeta, onMultiSort } = useMultipleColumnsSort(props);
const { presortConfig } = usePresort(props);
const { multipleSelectionConfig, selectedItems } = useMultipleRowSelection(props);
const { checkboxConfig } = useRadioCheckboxSelection(props);
const { conditionalStyleConfig, getRowClass, getRowStyle } = useConditionalStyle(props);
const { crudConfig, handleCrudAction } = useCrudActions(props, emit);

// Use Enterprise traits
const { apexWidgetConfig, getWidgetComponent, renderApexWidget } = useApexWidgets(props);
const { removableSortConfig } = useRemovableSort(props);

// Use composables
const { data, totalRecords, isLazyMode, loadData } = useLazy(props);
const { filterConfig } = useBasicFilter(props);
const { frozenRowsConfig } = useFrozenRows(props, emit);
const { frozenColumnsConfig } = useFrozenColumns(props, emit);
const { preloadConfig } = usePreload(props);
const { columnGroupConfig } = useColumnGroup(props);
const { rowGroupConfig } = useRowGroup(props);
const { columnToggleConfig } = useColumnToggle(props);
const { reorderConfig, onColumnReorder, onRowReorder } = useReorder(props, emit);
const { groupActionsConfig, executeGroupAction } = useGroupActions(props, selectedItems, emit);

// Use Enterprise composables
const { advancedFilterConfig } = useAdvancedFilter(props);
const { 
    rowExpansionConfig, 
    expandedRows, 
    onRowExpand, 
    onRowCollapse, 
    expandAll, 
    collapseAll,
    getExpansionTitle,
    getNestedWidgetConfig 
} = useRowExpansion(props, emit);
const { 
    contextMenuConfig, 
    contextMenuSelection, 
    contextMenuModel, 
    onRowContextMenu,
    handleContextMenuAction 
} = useContextMenu(props, emit);
const { exportConfig, exportData } = useExport(props);
const { enterpriseCrudConfig } = useEnterpriseCrudActions(props, emit);
const { enterpriseGroupActionsConfig } = useEnterpriseGroupActions(props, selectedItems, emit);

const dt = ref();
const cm = ref();

// Handle cell click with enhanced functionality
const handleCellClick = (data: any, column: Column) => {
    if (column.url) {
        // Handle URL navigation
        const url = column.url.replace(/{(\w+)}/g, (match, field) => data[field] || '');
        window.open(url, column.urlTarget || '_self');
    } else if (column.action) {
        // Emit custom action
        const actionField = column.actionField || props.crudActions?.idField || 'id';
        emit('action', {
            action: column.action,
            data: data,
            value: data[actionField]
        });
    }
};

// Computed table classes with Enterprise enhancements
const tableClasses = computed(() => {
    const classes = [];
    if (sizeConfig.value.size === 'small') classes.push('p-datatable-sm');
    if (sizeConfig.value.size === 'large') classes.push('p-datatable-lg');
    if (props.tableClass) classes.push(props.tableClass);
    return classes.join(' ');
});

// Initialize
onMounted(async () => {
    initVisibleColumns();
    await loadData();
});
</script>

<template>
    <div class="apex-datatable-widget enterprise-edition">
        <!-- Context Menu -->
        <PContextMenu 
            v-if="contextMenuConfig.hasContextMenu"
            ref="cm" 
            :model="contextMenuModel" 
            @hide="contextMenuSelection = null"
        />
        
        <!-- Header -->
        <div v-if="headerConfig" class="mb-4 rounded-t-lg border border-b-0 border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 v-if="headerConfig.title" class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ headerConfig.title }}
                    </h3>
                    <p v-if="headerConfig.subtitle" class="text-sm text-gray-600 dark:text-gray-400">
                        {{ headerConfig.subtitle }}
                    </p>
                </div>
                <div v-if="headerConfig.actions" class="flex gap-2">
                    <PButton
                        v-for="(action, idx) in headerConfig.actions"
                        :key="idx"
                        :label="action.label"
                        :icon="action.icon"
                        :severity="action.severity || 'secondary'"
                        size="small"
                        @click="$emit('headerAction', action.action)"
                    />
                </div>
            </div>
        </div>

        <!-- Advanced Toolbar -->
        <div v-if="searchConfig.globalFilter || (selectedItems.length > 0 && groupActionsConfig.groupActions.length > 0) || rowExpansionConfig.showExpandControls || exportConfig.exportable" 
             class="mb-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <!-- Global Filter -->
                <span v-if="searchConfig.globalFilter" class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <PInputText
                        v-model="globalFilterValue"
                        placeholder="Search all columns..."
                        @input="onGlobalFilter"
                        class="w-80"
                    />
                </span>

                <!-- Group Actions -->
                <template v-if="selectedItems.length > 0 && groupActionsConfig.groupActions.length > 0">
                    <PDivider layout="vertical" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ selectedItems.length }} selected
                    </span>
                    <PButton
                        v-for="(action, idx) in groupActionsConfig.groupActions"
                        :key="idx"
                        :label="action.label"
                        :icon="action.icon"
                        :severity="action.severity || 'danger'"
                        size="small"
                        @click="executeGroupAction(action)"
                    />
                </template>

                <!-- Row Expansion Controls -->
                <template v-if="rowExpansionConfig.showExpandControls">
                    <PDivider layout="vertical" />
                    <PButton
                        :label="rowExpansionConfig.expandAllLabel"
                        icon="pi pi-plus"
                        text
                        size="small"
                        @click="expandAll"
                    />
                    <PButton
                        :label="rowExpansionConfig.collapseAllLabel"
                        icon="pi pi-minus"
                        text
                        size="small"
                        @click="collapseAll"
                    />
                </template>
            </div>

            <!-- Export and Column Toggle -->
            <div class="flex items-center gap-2">
                <!-- Export Buttons -->
                <template v-if="exportConfig.exportable">
                    <PButton
                        v-for="format in exportConfig.exportFormats"
                        :key="format"
                        :label="`Export ${format.toUpperCase()}`"
                        icon="pi pi-download"
                        severity="secondary"
                        size="small"
                        @click="exportData(format)"
                    />
                </template>

                <!-- Column Toggle -->
                <template v-if="columnToggleConfig.columnToggle">
                    <PMultiSelect 
                        :modelValue="visibleColumns" 
                        :options="columns.filter(col => !col.frozen && !col.hidden)" 
                        optionLabel="header" 
                        @update:modelValue="onColumnToggle"
                        display="chip" 
                        placeholder="Select Columns"
                        class="w-full max-w-md"
                    />
                </template>
            </div>
        </div>

        <!-- DataTable -->
        <PDataTable
            ref="dt"
            :value="data"
            :loading="loading"
            :dataKey="dataKey"
            v-model:selection="selectedItems"
            v-model:expandedRows="expandedRows"
            v-model:contextMenuSelection="contextMenuSelection"
            :contextMenu="contextMenuConfig.hasContextMenu"
            :paginator="paginationConfig.paginator"
            :paginatorPosition="paginationConfig.paginatorPosition"
            :rows="paginationConfig.rows"
            :rowsPerPageOptions="paginationConfig.rowsPerPageOptions"
            :currentPageReportTemplate="paginationTemplateConfig.currentPageReportTemplate"
            :first="first"
            :totalRecords="totalRecords"
            :lazy="isLazyMode"
            :sortMode="multiSortConfig.sortMode"
            :removableSort="removableSortConfig.removableSort"
            :multiSortMeta="multiSortMeta"
            :selectionMode="multipleSelectionConfig.selectionMode"
            :metaKeySelection="multipleSelectionConfig.metaKeySelection"
            :globalFilterFields="searchConfig.globalFilterFields || columns.map(col => col.field)"
            :scrollable="scrollConfig.scrollable"
            :scrollHeight="scrollConfig.scrollHeight"
            :resizableColumns="resizeConfig.resizableColumns"
            :columnResizeMode="resizeConfig.columnResizeMode"
            :reorderableColumns="reorderConfig.effectiveReorderableColumns"
            :reorderableRows="reorderConfig.effectiveReorderableRows"
            :stripedRows="stripedRowsEnabled"
            :showGridlines="gridLinesConfig.showGridlines"
            :responsiveLayout="responsiveLayout"
            :stateStorage="statefulConfig.stateStorage"
            :stateKey="statefulConfig.stateKey"
            :tableStyle="tableStyle"
            :class="tableClasses"
            :rowClass="getRowClass"
            :rowStyle="getRowStyle"
            @page="onPage"
            @sort="onMultiSort"
            @column-reorder="onColumnReorder"
            @row-reorder="onRowReorder"
            @row-expand="onRowExpand"
            @row-collapse="onRowCollapse"
            @row-contextmenu="onRowContextMenu"
        >
            <!-- Column Group Header -->
            <PColumnGroup v-if="columnGroupConfig.hasHeaderGroups" type="header">
                <PRow v-for="(row, rowIndex) in columnGroupConfig.headerGroups" :key="`header-row-${rowIndex}`">
                    <PColumn
                        v-for="(cell, cellIndex) in row.cells"
                        :key="`header-cell-${rowIndex}-${cellIndex}`"
                        :header="columnGroupConfig.processCellContent(cell, data)"
                        :field="cell.field"
                        :sortable="cell.sortable"
                        :rowspan="cell.rowspan"
                        :colspan="cell.colspan"
                        :headerStyle="cell.headerStyle"
                    />
                </PRow>
            </PColumnGroup>

            <!-- Empty Message -->
            <template #empty>
                <div class="flex items-center justify-center p-8 text-gray-500">
                    {{ emptyMessage }}
                </div>
            </template>

            <!-- Loading -->
            <template #loading>
                <div class="flex items-center justify-center p-8">
                    <i class="pi pi-spin pi-spinner text-2xl"></i>
                </div>
            </template>

            <!-- Row Expander Column -->
            <PColumn 
                v-if="rowExpansionConfig.hasRowExpansion"
                expander
                :style="rowExpansionConfig.expanderColumnStyle"
                :frozen="rowExpansionConfig.expanderColumnFrozen"
                :exportable="false"
                :reorderableColumn="false"
                :resizeable="false"
            />

            <!-- Selection Column -->
            <PColumn
                v-if="checkboxConfig.showCheckboxColumn"
                selectionMode="multiple"
                :style="{ width: '3rem' }"
                :frozen="true"
                :exportable="false"
            />

            <!-- Data Columns -->
            <PColumn
                v-for="col in visibleColumns"
                :key="col.field"
                :field="col.field"
                :sortable="col.sortable"
                :style="col.style"
                :bodyStyle="col.bodyStyle"
                :headerStyle="col.headerStyle"
                :exportable="col.exportable !== false"
                :frozen="col.frozen || frozenColumnsConfig.isColumnLocked(col.field)"
                :resizeable="col.resizable !== false && resizeConfig.resizableColumns"
            >
                <!-- Column Header Template -->
                <template #header>
                    <span class="font-semibold">{{ col.header }}</span>
                </template>

                <!-- Column Body Template -->
                <template #body="slotProps">
                    <!-- ApexWidget Type (Enterprise) -->
                    <component
                        v-if="col.dataType === 'apexwidget' && col.widgetConfig"
                        :is="getWidgetComponent(col.widgetConfig.type)"
                        v-bind="{ ...col.widgetConfig, value: slotProps.data[col.field] }"
                    />
                    
                    <!-- Image Type -->
                    <img
                        v-else-if="col.dataType === 'image'"
                        :src="slotProps.data[col.field]"
                        :alt="col.header"
                        :style="{ width: typeof col.format === 'number' ? `${col.format}px` : col.format }"
                        class="h-auto"
                    />
                    
                    <!-- Clickable Content -->
                    <a
                        v-else-if="col.url || col.clickable"
                        href="#"
                        @click.prevent="handleCellClick(slotProps.data, col)"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        {{ formatCellValue(slotProps.data[col.field], col) }}
                    </a>
                    
                    <!-- Regular Content -->
                    <span v-else>
                        {{ formatCellValue(slotProps.data[col.field], col) }}
                    </span>
                </template>
            </PColumn>

            <!-- CRUD Action Columns -->
            <PColumn
                v-if="crudConfig.showView"
                field="_action_view"
                header=""
                :sortable="false"
                :exportable="false"
                :frozen="true"
                style="width: 50px"
            >
                <template #body="slotProps">
                    <PButton
                        icon="pi pi-eye"
                        rounded
                        severity="success"
                        outlined
                        size="small"
                        @click="handleCrudAction('view', slotProps.data)"
                        v-tooltip="'View'"
                    />
                </template>
            </PColumn>

            <PColumn
                v-if="crudConfig.showEdit"
                field="_action_edit"
                header=""
                :sortable="false"
                :exportable="false"
                :frozen="true"
                style="width: 50px"
            >
                <template #body="slotProps">
                    <PButton
                        icon="pi pi-pen-to-square"
                        rounded
                        severity="info"
                        outlined
                        size="small"
                        @click="handleCrudAction('edit', slotProps.data)"
                        v-tooltip="'Edit'"
                    />
                </template>
            </PColumn>

            <PColumn
                v-if="crudConfig.showDelete"
                field="_action_delete"
                header=""
                :sortable="false"
                :exportable="false"
                :frozen="true"
                style="width: 50px"
            >
                <template #body="slotProps">
                    <PButton
                        icon="pi pi-eraser"
                        rounded
                        severity="danger"
                        outlined
                        size="small"
                        @click="handleCrudAction('delete', slotProps.data)"
                        v-tooltip="'Delete'"
                    />
                </template>
            </PColumn>

            <!-- Row Expansion Template -->
            <template v-if="rowExpansionConfig.hasRowExpansion" #expansion="slotProps">
                <div class="p-4">
                    <!-- Expansion title -->
                    <h5 v-if="getExpansionTitle(slotProps.data)" class="mb-4 text-lg font-semibold">
                        {{ getExpansionTitle(slotProps.data) }}
                    </h5>
                    
                    <!-- Nested DataTable Widget -->
                    <template v-if="rowExpansionConfig.expandedContentType === 'datatable'">
                        <template v-if="getNestedWidgetConfig(slotProps.data)">
                            <WidgetRenderer 
                                :widgets="[getNestedWidgetConfig(slotProps.data)!]"
                                @action="$emit('action', $event)"
                                @crud-action="$emit('crud-action', $event)"
                            />
                        </template>
                    </template>
                    
                    <!-- Custom content -->
                    <template v-else-if="rowExpansionConfig.expandedContentType === 'custom'">
                        <div v-html="rowExpansionConfig.customTemplate"></div>
                    </template>
                </div>
            </template>

            <!-- Column Group Footer -->
            <PColumnGroup v-if="columnGroupConfig.hasFooterGroups" type="footer">
                <PRow v-for="(row, rowIndex) in columnGroupConfig.footerGroups" :key="`footer-row-${rowIndex}`">
                    <PColumn
                        v-for="(cell, cellIndex) in row.cells"
                        :key="`footer-cell-${rowIndex}-${cellIndex}`"
                        :footer="columnGroupConfig.processCellContent(cell, data)"
                        :rowspan="cell.rowspan"
                        :colspan="cell.colspan"
                        :footerStyle="cell.footerStyle"
                    />
                </PRow>
            </PColumnGroup>
        </PDataTable>

        <!-- Footer -->
        <div v-if="footerConfig && (footerConfig.showRecordCount || footerConfig.text || footerConfig.showSelectedCount)" 
             class="rounded-b-lg border border-t-0 border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span v-if="footerConfig.showRecordCount">
                        Total Records: {{ totalRecords }}
                    </span>
                    <span v-if="footerConfig.showRecordCount && footerConfig.showSelectedCount && selectedItems.length > 0" class="mx-2">|</span>
                    <span v-if="footerConfig.showSelectedCount && selectedItems.length > 0">
                        Selected: {{ selectedItems.length }}
                    </span>
                    <span v-if="rowExpansionConfig.hasRowExpansion" class="mx-2">|</span>
                    <span v-if="rowExpansionConfig.hasRowExpansion">
                        Expandable Rows: {{ Object.keys(expandedRows).length }} expanded
                    </span>
                </div>
                <div v-if="footerConfig.text">
                    {{ footerConfig.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.apex-datatable-widget.enterprise-edition {
    width: 100%;
}

.apex-datatable-widget.enterprise-edition :deep(.p-datatable.p-datatable-sm) {
    font-size: 0.75rem;
}

.apex-datatable-widget.enterprise-edition :deep(.p-datatable.p-datatable-lg) {
    font-size: 1rem;
}

/* Enterprise-specific styling enhancements */
.apex-datatable-widget.enterprise-edition :deep(.p-datatable-tbody > tr.conditional-highlight) {
    background-color: rgba(59, 130, 246, 0.1);
}

/* Context Menu custom icon styling */
.apex-datatable-widget.enterprise-edition :deep(.p-menuitem-with-image .p-menuitem-icon-image) {
    width: 1rem;
    height: 1rem;
    margin-right: 0.5rem;
    vertical-align: middle;
}

/* Row expansion styling */
.apex-datatable-widget.enterprise-edition :deep(.p-datatable-row-expansion) {
    background-color: rgba(243, 244, 246, 0.5);
}

/* ApexWidget column styling */
.apex-datatable-widget.enterprise-edition :deep(.apex-widget-column) {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>