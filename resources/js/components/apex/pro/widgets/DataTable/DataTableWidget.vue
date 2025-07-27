<!-- resources/js/components/apex/pro/widgets/DataTable/DataTableWidget.vue -->
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import PDataTable from 'primevue/datatable';
import PColumn from 'primevue/column';
import PColumnGroup from 'primevue/columngroup';
import PRow from 'primevue/row';
import PInputText from 'primevue/inputtext';
import PButton from 'primevue/button';
import PMultiSelect from 'primevue/multiselect';

// Core traits (inherited)
import { useDynamicColumns } from '../../core/widgets/DataTable/traits/useDynamicColumns.js';
import { useTemplate } from '../../core/widgets/DataTable/traits/useTemplate.js';
import { useStripedRows } from '../../core/widgets/DataTable/traits/useStripedRows.js';
import { usePagination } from '../../core/widgets/DataTable/traits/usePagination.js';
import { usePaginationTemplate } from '../../core/widgets/DataTable/traits/usePaginationTemplate.js';
import { useSingleColumnSort } from '../../core/widgets/DataTable/traits/useSingleColumnSort.js';
import { useSingleRowSelection } from '../../core/widgets/DataTable/traits/useSingleRowSelection.js';
import { useScroll } from '../../core/widgets/DataTable/traits/useScroll.js';
import { useGridLines } from '../../core/widgets/DataTable/traits/useGridLines.js';
import { useColumnResize } from '../../core/widgets/DataTable/traits/useColumnResize.js';
import { useSearch } from '../../core/widgets/DataTable/traits/useSearch.js';
import { useStateful } from '../../core/widgets/DataTable/traits/useStateful.js';

// Pro traits
import { useDataFormatting } from './traits/useDataFormatting.js';
import { useSize } from './traits/useSize.js';
import { useMultipleColumnsSort } from './traits/useMultipleColumnsSort.js';
import { usePresort } from './traits/usePresort.js';
import { useMultipleRowSelection } from './traits/useMultipleRowSelection.js';
import { useRadioCheckboxSelection } from './traits/useRadioCheckboxSelection.js';
import { useConditionalStyle } from './traits/useConditionalStyle.js';
import { useCrudActions } from './traits/useCrudActions.js';

// Core composables (inherited)
import { useLazy } from '../../core/widgets/DataTable/composables/useLazy.js';

// Pro composables
import { useBasicFilter } from './composables/useBasicFilter.js';
import { useFrozenRows } from './composables/useFrozenRows.js';
import { useFrozenColumns } from './composables/useFrozenColumns.js';
import { usePreload } from './composables/usePreload.js';
import { useColumnGroup } from './composables/useColumnGroup.js';
import { useRowGroup } from './composables/useRowGroup.js';
import { useColumnToggle } from './composables/useColumnToggle.js';
import { useReorder } from './composables/useReorder.js';
import { useGroupActions } from './composables/useGroupActions.js';

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
    // Pro-specific props
    size?: 'small' | 'normal' | 'large';
    sortMode?: 'single' | 'multiple';
    selectionMode?: 'single' | 'multiple' | 'checkbox';
    conditionalStyles?: any[];
    // And many more Pro features...
}

const props = withDefaults(defineProps<Props>(), {
    dataKey: 'id',
    loading: false,
    emptyMessage: 'No records found',
    tableStyle: 'min-width: 50rem',
    responsiveLayout: 'scroll',
    size: 'normal',
    sortMode: 'single'
});

const emit = defineEmits<{
    action: [payload: { action: string; data: any; value: any }];
    headerAction: [action: string];
    'crud-action': [payload: { action: string; id: any; data: any }];
    'group-action': [payload: { action: string; items: any[] }];
}>();

// Use Core traits
const {
    visibleColumns,
    initVisibleColumns,
    onColumnToggle
} = useDynamicColumns(props);

const { headerConfig, footerConfig } = useTemplate(props);
const { stripedRowsEnabled } = useStripedRows(props);
const { paginationConfig, first, onPage } = usePagination(props);
const { paginationTemplateConfig } = usePaginationTemplate(props);
const { searchConfig, globalFilterValue, onGlobalFilter } = useSearch(props);
const { statefulConfig } = useStateful(props);

// Use Pro traits
const { sizeConfig } = useSize(props);
const { multiSortConfig, multiSortMeta, onMultiSort } = useMultipleColumnsSort(props);
const { presortConfig } = usePresort(props);
const { multipleSelectionConfig, selectedItems } = useMultipleRowSelection(props);
const { checkboxConfig } = useRadioCheckboxSelection(props);
const { conditionalStyleConfig, getRowClass, getRowStyle } = useConditionalStyle(props);
const { crudConfig, handleCrudAction } = useCrudActions(props);

// Use composables
const { data, totalRecords, isLazyMode, loadData } = useLazy(props);
const { filterConfig } = useBasicFilter(props);
const { frozenRowsConfig } = useFrozenRows(props);
const { frozenColumnsConfig } = useFrozenColumns(props);
const { preloadConfig } = usePreload(props);
const { columnGroupConfig } = useColumnGroup(props);
const { rowGroupConfig } = useRowGroup(props);
const { columnToggleConfig } = useColumnToggle(props);
const { reorderConfig, onColumnReorder, onRowReorder } = useReorder(props);
const { groupActionsConfig, executeGroupAction } = useGroupActions(props);

const dt = ref();

// Format cell value based on data type (Pro feature)
const formatCellValue = (value: any, column: Column): string => {
    if (value === null || value === undefined) return '';
    
    let formattedValue = '';
    
    switch (column.dataType) {
        case 'currency':
            const decimals = typeof column.format === 'number' ? column.format : 2;
            formattedValue = parseFloat(value).toFixed(decimals);
            break;
            
        case 'percentage':
            const percentDecimals = typeof column.format === 'number' ? column.format : 2;
            formattedValue = parseFloat(value).toFixed(percentDecimals);
            break;
            
        case 'number':
            const numberDecimals = typeof column.format === 'number' ? column.format : 0;
            formattedValue = parseFloat(value).toFixed(numberDecimals);
            break;
            
        default:
            formattedValue = String(value);
    }
    
    // Add lead and trail text
    return `${column.leadText || ''}${formattedValue}${column.trailText || ''}`;
};

// Computed table classes with size support
const tableClasses = computed(() => {
    const classes = [];
    if (sizeConfig.value.size === 'small') classes.push('p-datatable-sm');
    if (sizeConfig.value.size === 'large') classes.push('p-datatable-lg');
    // Add other classes from gridLines trait, etc.
    return classes.join(' ');
});

// Initialize
onMounted(async () => {
    initVisibleColumns();
    await loadData();
});
</script>

<template>
    <div class="apex-datatable-widget pro-edition">
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

        <!-- Toolbar -->
        <div v-if="searchConfig.globalFilter || (selectedItems.length > 0 && groupActionsConfig.groupActions.length > 0) || columnToggleConfig.columnToggle" 
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
            </div>

            <!-- Column Toggle -->
            <div v-if="columnToggleConfig.columnToggle" class="flex items-center gap-2">
                <PMultiSelect 
                    :modelValue="visibleColumns" 
                    :options="columns.filter(col => !col.frozen && !col.hidden)" 
                    optionLabel="header" 
                    @update:modelValue="onColumnToggle"
                    display="chip" 
                    placeholder="Select Columns"
                    class="w-full max-w-md"
                />
            </div>
        </div>

        <!-- DataTable -->
        <PDataTable
            ref="dt"
            :value="data"
            :loading="loading"
            :dataKey="dataKey"
            v-model:selection="selectedItems"
            :paginator="paginationConfig.paginator"
            :paginatorPosition="paginationConfig.paginatorPosition"
            :rows="paginationConfig.rows"
            :rowsPerPageOptions="paginationConfig.rowsPerPageOptions"
            :currentPageReportTemplate="paginationTemplateConfig.currentPageReportTemplate"
            :first="first"
            :totalRecords="totalRecords"
            :lazy="isLazyMode"
            :sortMode="multiSortConfig.sortMode"
            :multiSortMeta="multiSortMeta"
            :selectionMode="multipleSelectionConfig.selectionMode"
            :metaKeySelection="multipleSelectionConfig.metaKeySelection"
            :globalFilterFields="searchConfig.globalFilterFields || columns.map(col => col.field)"
            :scrollable="scrollConfig.scrollable"
            :scrollHeight="scrollConfig.scrollHeight"
            :resizableColumns="resizeConfig.resizableColumns"
            :columnResizeMode="resizeConfig.columnResizeMode"
            :reorderableColumns="reorderConfig.reorderableColumns"
            :reorderableRows="reorderConfig.reorderableRows"
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
        >
            <!-- Column Group Header -->
            <PColumnGroup v-if="columnGroupConfig.hasHeaderGroups" type="header">
                <PRow v-for="(row, rowIndex) in columnGroupConfig.headerGroups" :key="`header-row-${rowIndex}`">
                    <PColumn
                        v-for="(cell, cellIndex) in row.cells"
                        :key="`header-cell-${rowIndex}-${cellIndex}`"
                        :header="cell.header"
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
                :frozen="col.frozen || frozenColumnsConfig.isColumnFrozen(col.field)"
                :resizeable="col.resizable !== false && resizeConfig.resizableColumns"
            >
                <!-- Column Header Template -->
                <template #header>
                    <span class="font-semibold">{{ col.header }}</span>
                </template>

                <!-- Column Body Template -->
                <template #body="slotProps">
                    <span>{{ formatCellValue(slotProps.data[col.field], col) }}</span>
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

            <!-- Column Group Footer -->
            <PColumnGroup v-if="columnGroupConfig.hasFooterGroups" type="footer">
                <PRow v-for="(row, rowIndex) in columnGroupConfig.footerGroups" :key="`footer-row-${rowIndex}`">
                    <PColumn
                        v-for="(cell, cellIndex) in row.cells"
                        :key="`footer-cell-${rowIndex}-${cellIndex}`"
                        :footer="cell.footer"
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
                </div>
                <div v-if="footerConfig.text">
                    {{ footerConfig.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.apex-datatable-widget.pro-edition {
    width: 100%;
}

.apex-datatable-widget.pro-edition :deep(.p-datatable.p-datatable-sm) {
    font-size: 0.75rem;
}

.apex-datatable-widget.pro-edition :deep(.p-datatable.p-datatable-lg) {
    font-size: 1rem;
}

/* Pro-specific styling enhancements */
.apex-datatable-widget.pro-edition :deep(.p-datatable-tbody > tr.conditional-highlight) {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>