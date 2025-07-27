<!-- resources/js/components/apex/core/widgets/DataTable/DataTableWidget.vue -->
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import PDataTable from 'primevue/datatable';
import PColumn from 'primevue/column';
import PInputText from 'primevue/inputtext';
import PButton from 'primevue/button';

// Core traits
import { useDynamicColumns } from './traits/useDynamicColumns.js';
import { useTemplate } from './traits/useTemplate.js';
import { useStripedRows } from './traits/useStripedRows.js';
import { usePagination } from './traits/usePagination.js';
import { usePaginationTemplate } from './traits/usePaginationTemplate.js';
import { useSingleColumnSort } from './traits/useSingleColumnSort.js';
import { useSingleRowSelection } from './traits/useSingleRowSelection.js';
import { useScroll } from './traits/useScroll.js';
import { useGridLines } from './traits/useGridLines.js';
import { useColumnResize } from './traits/useColumnResize.js';
import { useSearch } from './traits/useSearch.js';
import { useStateful } from './traits/useStateful.js';

// Core composables
import { useLazy } from './composables/useLazy.js';

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
}

const props = withDefaults(defineProps<Props>(), {
    dataKey: 'id',
    loading: false,
    emptyMessage: 'No records found',
    tableStyle: 'min-width: 50rem',
    responsiveLayout: 'scroll'
});

const emit = defineEmits<{
    action: [payload: { action: string; data: any; value: any }];
    headerAction: [action: string];
}>();

// Use Core traits
const {
    visibleColumns,
    initVisibleColumns,
    onColumnToggle
} = useDynamicColumns(props);

const {
    headerConfig,
    footerConfig
} = useTemplate(props);

const {
    stripedRowsEnabled
} = useStripedRows(props);

const {
    paginationConfig,
    first,
    onPage
} = usePagination(props);

const {
    paginationTemplateConfig
} = usePaginationTemplate(props);

const {
    sortConfig,
    sortField,
    sortOrder,
    onSort
} = useSingleColumnSort(props);

const {
    selectionConfig,
    selectedItems
} = useSingleRowSelection(props);

const {
    scrollConfig
} = useScroll(props);

const {
    gridLinesConfig,
    tableClasses
} = useGridLines(props);

const {
    resizeConfig
} = useColumnResize(props);

const {
    searchConfig,
    globalFilterValue,
    onGlobalFilter
} = useSearch(props);

const {
    statefulConfig
} = useStateful(props);

// Use Core composables
const {
    data,
    totalRecords,
    isLazyMode,
    loadData
} = useLazy(props);

const dt = ref();

// Initialize
onMounted(async () => {
    initVisibleColumns();
    await loadData();
});
</script>

<template>
    <div class="apex-datatable-widget">
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
        <div v-if="searchConfig.globalFilter" class="mb-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <!-- Global Filter -->
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <PInputText
                        v-model="globalFilterValue"
                        placeholder="Search all columns..."
                        @input="onGlobalFilter"
                        class="w-80"
                    />
                </span>
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
            :sortMode="sortConfig.sortMode"
            :defaultSortOrder="sortConfig.defaultSortOrder"
            :sortField="sortField"
            :sortOrder="sortOrder"
            :selectionMode="selectionConfig.selectionMode"
            :metaKeySelection="selectionConfig.metaKeySelection"
            :globalFilterFields="searchConfig.globalFilterFields || columns.map(col => col.field)"
            :scrollable="scrollConfig.scrollable"
            :scrollHeight="scrollConfig.scrollHeight"
            :virtualScrollerOptions="scrollConfig.virtualScroll ? { itemSize: 46 } : undefined"
            :resizableColumns="resizeConfig.resizableColumns"
            :columnResizeMode="resizeConfig.columnResizeMode"
            :stripedRows="stripedRowsEnabled"
            :showGridlines="gridLinesConfig.showGridlines"
            :responsiveLayout="responsiveLayout"
            :stateStorage="statefulConfig.stateStorage"
            :stateKey="statefulConfig.stateKey"
            :tableStyle="tableStyle"
            :class="tableClasses"
            @page="onPage"
            @sort="onSort"
        >
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
                :frozen="col.frozen"
                :resizeable="col.resizable !== false && resizeConfig.resizableColumns"
            >
                <!-- Column Header Template -->
                <template #header>
                    <span class="font-semibold">{{ col.header }}</span>
                </template>

                <!-- Column Body Template -->
                <template #body="slotProps">
                    <span>{{ slotProps.data[col.field] }}</span>
                </template>
            </PColumn>
        </PDataTable>

        <!-- Footer -->
        <div v-if="footerConfig && (footerConfig.showRecordCount || footerConfig.text)" 
             class="rounded-b-lg border border-t-0 border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span v-if="footerConfig.showRecordCount">
                        Total Records: {{ totalRecords }}
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
.apex-datatable-widget {
    width: 100%;
}

.apex-datatable :deep(.p-datatable) {
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.apex-datatable :deep(.p-paginator) {
    border-width: 0;
    background-color: transparent;
}
</style>