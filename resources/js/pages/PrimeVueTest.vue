// resources/js/pages/PrimeVueTest.vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Props {
    widgets: any[];
}

const props = defineProps<Props>();

// Reactive data
const counter = ref(0);
const temperature = ref(20);

// Methods
const increment = () => {
    counter.value++;
};

const decrement = () => {
    counter.value--;
};

// Separate widgets by type
const breadcrumbWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'breadcrumb')
);

const knobWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'knob')
);

const datePickerWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'datepicker')
);

const dataTableWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'datatable')
);

// Separate DataTables by their characteristics for better organization
const serverSideDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Product Inventory Management')
);

const clientSideDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Client-Side DataTable')
);

const autoModeDataTables = computed(() => 
    dataTableWidgets.value.filter(w => w.props.header?.title?.includes('Auto Mode'))
);

const advancedColumnDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Advanced Column Features')
);

const actionDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Products with Actions')
);

// Handle custom actions from DataTable
const handleCustomAction = (payload: any) => {
    console.log('Custom Action:', payload);
    alert(`Action: ${payload.action}\nValue: ${payload.value}\nData: ${JSON.stringify(payload.data, null, 2)}`);
};

// Handle CRUD actions from DataTable
const handleCrudAction = (payload: any) => {
    console.log('CRUD Action:', payload);
    
    switch (payload.action) {
        case 'view':
            alert(`View Product ID: ${payload.id}`);
            break;
        case 'edit':
            alert(`Edit Product ID: ${payload.id}`);
            break;
        case 'delete':
            if (confirm(`Are you sure you want to delete product ID: ${payload.id}?`)) {
                alert(`Delete Product ID: ${payload.id}`);
            }
            break;
        case 'history':
            alert(`View History for Product ID: ${payload.id}`);
            break;
        case 'print':
            alert(`Print Product ID: ${payload.id}`);
            window.print();
            break;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'PrimeVue Test',
        href: '/primevue-test',
    },
];
</script>

<template>
    <Head title="PrimeVue Test" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="mx-auto max-w-7xl">
                <h1 class="mb-8 text-3xl font-bold text-gray-900 dark:text-white">
                    PrimeVue Component Test
                </h1>

                <!-- APEX Widget System Section -->
                <PCard class="mb-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">APEX JSON Widget System</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-8 p-6">
                            <!-- Breadcrumb Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Breadcrumb Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Navigation breadcrumbs rendered from JSON configuration:
                                </p>
                                <WidgetRenderer :widgets="breadcrumbWidgets" />
                            </div>

                            <!-- Knob Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Knob Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Interactive knob controls rendered from JSON configuration:
                                </p>
                                <div class="flex flex-wrap items-center justify-around gap-6 rounded-lg bg-gray-50 p-6 dark:bg-gray-800">
                                    <WidgetRenderer :widgets="knobWidgets" />
                                </div>
                            </div>

                            <!-- DatePicker Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    DatePicker Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Date selection widgets with various configurations:
                                </p>
                                <div class="grid gap-6 md:grid-cols-3">
                                    <WidgetRenderer :widgets="datePickerWidgets" />
                                </div>
                            </div>

                            <!-- DataTable Widgets -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    DataTable Widgets
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Server-side data tables with sorting, filtering, and pagination:
                                </p>
                                
                                <!-- Server-side DataTable -->
                                <div v-if="serverSideDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Full-featured DataTable with Server-side Processing
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <WidgetRenderer :widgets="[serverSideDataTable]" />
                                    </div>
                                </div>

                                <!-- Client-side DataTable -->
                                <div v-if="clientSideDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Simple Client-side DataTable
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <WidgetRenderer :widgets="[clientSideDataTable]" />
                                    </div>
                                </div>

                                <!-- Auto Mode DataTables -->
                                <div v-if="autoModeDataTables.length > 0" class="space-y-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Auto Mode DataTables - Intelligent Loading Strategy
                                    </h4>
                                    <div v-for="(table, index) in autoModeDataTables" :key="`auto-${index}`" 
                                         class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <WidgetRenderer :widgets="[table]" />
                                    </div>
                                </div>

                                <!-- Advanced Column Features DataTable -->
                                <div v-if="advancedColumnDataTable" class="mt-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Advanced Column Features Demo
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
                                            <div class="mb-2 font-semibold">Try these features:</div>
                                            <ul class="list-inside list-disc space-y-1">
                                                <li>Click "Columns" button to show/hide columns</li>
                                                <li>Drag column borders to resize (except Price column)</li>
                                                <li>Drag column headers to reorder</li>
                                                <li>ID and Stock columns are hidden by default</li>
                                                <li>Code column is frozen and cannot be hidden</li>
                                            </ul>
                                        </div>
                                        <WidgetRenderer :widgets="[advancedColumnDataTable]" />
                                    </div>
                                </div>

                                <!-- Action Handler DataTable -->
                                <div v-if="actionDataTable" class="mt-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        DataTable with Action Handlers
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-purple-50 p-4 text-sm text-purple-800 dark:bg-purple-900/20 dark:text-purple-200">
                                            <div class="mb-2 font-semibold">Interactive Features:</div>
                                            <ul class="list-inside list-disc space-y-1">
                                                <li><strong>Product Names:</strong> Click to open details in new tab</li>
                                                <li><strong>Categories:</strong> Click to trigger filter action</li>
                                                <li><strong>Action Buttons:</strong> View, Edit, History, Print (Delete disabled by permission)</li>
                                                <li><strong>Events:</strong> Check console for action events</li>
                                            </ul>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[actionDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>

                <!-- Static PrimeVue Components Section -->
                <PCard class="shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Static PrimeVue Components</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-8 p-6">
                            <!-- Basic Components -->
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Basic Components</h3>
                                
                                <div class="space-y-6">
                                    <!-- Buttons -->
                                    <div>
                                        <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">Buttons</h4>
                                        <div class="flex flex-wrap gap-3">
                                            <PButton label="Primary" />
                                            <PButton label="Secondary" severity="secondary" />
                                            <PButton label="Success" severity="success" />
                                            <PButton label="Warning" severity="warning" />
                                            <PButton label="Danger" severity="danger" />
                                            <PButton label="Help" severity="help" />
                                            <PButton label="Info" severity="info" />
                                        </div>
                                    </div>

                                    <!-- Input Fields -->
                                    <div>
                                        <h4 class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-400">Input Fields</h4>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <PInputText placeholder="Enter text..." />
                                            <PInputNumber v-model="counter" showButtons :min="0" :max="100" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>
            </div>
        </div>
    </AppLayout>
</template>