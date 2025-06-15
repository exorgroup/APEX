// resources/js/components/apex/widgets/DataTableWidget.vue
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

interface Column {
    field: string;
    header: string;
    sortable?: boolean;
    filter?: boolean;
    style?: string;
    exportable?: boolean;
}

interface DataSource {
    url: string;
    method?: 'GET' | 'POST';
    lazy?: boolean;
}

interface Props {
    widgetId: string;
    columns: Column[];
    dataKey?: string;
    paginator?: boolean;
    rows?: number;
    rowsPerPageOptions?: number[];
    sortMode?: 'single' | 'multiple';
    globalFilter?: boolean;
    exportable?: boolean;
    selectionMode?: 'single' | 'multiple' | null;
    dataSource?: DataSource;
    tableStyle?: string;
    stripedRows?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    dataKey: 'id',
    paginator: true,
    rows: 10,
    rowsPerPageOptions: () => [5, 10, 25, 50],
    sortMode: 'single',
    globalFilter: false,
    exportable: false,
    selectionMode: null,
    tableStyle: 'min-width: 50rem',
    stripedRows: true
});

// State
const loading = ref(false);
const data = ref<any[]>([]);
const totalRecords = ref(0);
const filters = ref({});
const lazyParams = ref({});
const selectedItems = ref<any[]>([]);
const globalFilterValue = ref('');

// Computed properties
const hasLazyLoading = computed(() => props.dataSource?.lazy ?? false);

// Load data from server
const loadData = async (event: any = null) => {
    if (!props.dataSource?.url) return;

    loading.value = true;

    try {
        const params = hasLazyLoading.value ? {
            page: event?.page ?? 0,
            rows: event?.rows ?? props.rows,
            sortField: event?.sortField,
            sortOrder: event?.sortOrder,
            filters: event?.filters ?? {},
            globalFilter: globalFilterValue.value
        } : {};

        const response = await axios({
            method: props.dataSource.method || 'GET',
            url: props.dataSource.url,
            params: props.dataSource.method === 'GET' ? params : undefined,
            data: props.dataSource.method === 'POST' ? params : undefined
        });

        if (hasLazyLoading.value) {
            data.value = response.data.data;
            totalRecords.value = response.data.total;
        } else {
            data.value = response.data;
            totalRecords.value = response.data.length;
        }
    } catch (error) {
        console.error('Error loading data:', error);
    } finally {
        loading.value = false;
    }
};

// Event handlers
const onPage = (event: any) => {
    lazyParams.value = event;
    if (hasLazyLoading.value) {
        loadData(event);
    }
};

const onSort = (event: any) => {
    lazyParams.value = event;
    if (hasLazyLoading.value) {
        loadData(event);
    }
};

const onFilter = (event: any) => {
    lazyParams.value = event;
    filters.value = event.filters;
    if (hasLazyLoading.value) {
        loadData(event);
    }
};

const onGlobalFilter = () => {
    const event = { ...lazyParams.value, globalFilter: globalFilterValue.value };
    if (hasLazyLoading.value) {
        loadData(event);
    }
};

// Export functionality
const exportCSV = () => {
    // This would be implemented based on your export requirements
    console.log('Export CSV functionality');
};

// Load initial data
onMounted(() => {
    if (props.dataSource?.url) {
        loadData({ page: 0, rows: props.rows });
    }
});
</script>

<template>
    <div class="apex-datatable-widget">
        <div v-if="globalFilter || exportable" class="mb-4 flex items-center justify-between">
            <PInputText
                v-if="globalFilter"
                v-model="globalFilterValue"
                placeholder="Search..."
                @input="onGlobalFilter"
                class="max-w-md"
            />
            <PButton
                v-if="exportable"
                label="Export"
                icon="pi pi-download"
                severity="secondary"
                @click="exportCSV"
            />
        </div>

        <PDataTable
            :value="data"
            :loading="loading"
            :paginator="paginator"
            :rows="rows"
            :rowsPerPageOptions="rowsPerPageOptions"
            :totalRecords="totalRecords"
            :lazy="hasLazyLoading"
            :dataKey="dataKey"
            :sortMode="sortMode"
            :stripedRows="stripedRows"
            :tableStyle="tableStyle"
            :filters="filters"
            :globalFilterFields="columns.map(col => col.field)"
            v-model:selection="selectedItems"
            :selectionMode="selectionMode"
            @page="onPage"
            @sort="onSort"
            @filter="onFilter"
            responsiveLayout="scroll"
            class="apex-datatable"
        >
            <template #empty>
                <div class="flex items-center justify-center p-8 text-gray-500">
                    No data available
                </div>
            </template>

            <template #loading>
                <div class="flex items-center justify-center p-8">
                    <i class="pi pi-spin pi-spinner text-2xl"></i>
                </div>
            </template>

            <PColumn
                v-if="selectionMode"
                selectionMode="multiple"
                :style="{ width: '3rem' }"
                :exportable="false"
            />

            <PColumn
                v-for="col in columns"
                :key="col.field"
                :field="col.field"
                :header="col.header"
                :sortable="col.sortable"
                :filter="col.filter"
                :filterMatchMode="'contains'"
                :style="col.style"
                :exportable="col.exportable !== false"
            >
                <template #body="slotProps">
                    {{ slotProps.data[col.field] }}
                </template>

                <template v-if="col.filter" #filter="{ filterModel }">
                    <PInputText
                        v-model="filterModel.value"
                        type="text"
                        class="p-column-filter"
                        placeholder="Search..."
                    />
                </template>
            </PColumn>
        </PDataTable>
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