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

// DD20250712-1930 BEGIN - Add row expansion demo widget
const rowExpansionDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Products with Order History - Row Expansion Demo')
);
// DD20250712-1930 END

// DD20250710-1240 - Add conditional styling DataTable
const conditionalStylingDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Conditional Row Styling Demo')
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

const dataTypesDataTable = computed(() => 
    dataTableWidgets.value.find(w => w.props.header?.title === 'Data Type Formatting Demo')
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

// DD20250712-1930 BEGIN - Handle row expansion events
const handleRowExpansion = (event: any) => {
    console.log('Row Expanded:', event);
    // Optional: Show toast notification or perform other actions
};

const handleRowCollapse = (event: any) => {
    console.log('Row Collapsed:', event);
    // Optional: Show toast notification or perform other actions
};

const handleHeaderAction = (action: string) => {
    console.log('Header Action:', action);
    
    switch (action) {
        case 'add':
            alert('Add new product clicked');
            break;
        case 'import':
            alert('Import products clicked');
            break;
        case 'refresh':
            alert('Refresh data clicked');
            break;
        default:
            alert(`Header action: ${action}`);
    }
};
// DD20250712-1930 END

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

                                <!-- DD20250712-1930 BEGIN - Row Expansion Demo Section -->
                                <div v-if="rowExpansionDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🆕 Row Expansion Feature Demo - LATEST NEW FEATURE!
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 p-4 text-sm dark:from-blue-900/20 dark:to-purple-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                🚀 Row Expansion with Nested DataTables
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-1">
                                                <div class="space-y-2">
                                                    <div class="text-blue-800 dark:text-blue-200">
                                                        <strong>✨ New Features:</strong>
                                                    </div>
                                                    <ul class="list-inside list-disc space-y-1 text-sm text-blue-700 dark:text-blue-300">
                                                        <li><strong>Expandable Rows:</strong> Click the expand icon (▶) to view nested order history</li>
                                                        <li><strong>Nested DataTables:</strong> Full DataTableWidget with sorting, formatting, and styling</li>
                                                        <li><strong>Expand/Collapse All:</strong> Header buttons to expand or collapse all rows at once</li>
                                                        <li><strong>Dynamic Titles:</strong> Custom titles like "Order History for [Product Name]"</li>
                                                        <li><strong>Event Handling:</strong> Row expand/collapse events (check console)</li>
                                                        <li><strong>Conditional Styling:</strong> Color-coded rows based on inventory status</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Implementation:</strong> Uses nested DataTableWidgets with full functionality - export, search, styling all work in nested tables too!
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Future-Proof:</strong> All future DataTableWidget features automatically work in expanded rows
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[rowExpansionDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                            @header-action="handleHeaderAction"
                                            @row-expand="handleRowExpansion"
                                            @row-collapse="handleRowCollapse"
                                        />
                                    </div>
                                </div>
                                <!-- DD20250712-1930 END -->

                                 <!-- DD20250710-1240 - Conditional Styling DataTable -->
                                 <div v-if="conditionalStylingDataTable" class="mb-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        🎨 Conditional Row Styling Demo
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-gradient-to-r from-green-50 to-blue-50 p-4 text-sm dark:from-green-900/20 dark:to-blue-900/20">
                                            <div class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                                ✨ Conditional Styling Rules Demonstration
                                            </div>
                                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-red-400 bg-red-200"></div>
                                                    <span class="text-red-800 dark:text-red-200"><strong>Red:</strong> Out of Stock</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-orange-400 bg-orange-200"></div>
                                                    <span class="text-orange-800 dark:text-orange-200"><strong>Orange:</strong> Low Stock</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-green-400 bg-green-200"></div>
                                                    <span class="text-green-800 dark:text-green-200"><strong>Green:</strong> In Stock</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-blue-400 bg-blue-200"></div>
                                                    <span class="text-blue-800 dark:text-blue-200"><strong>Blue:</strong> Price > $100 (italic)</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <div class="h-4 w-4 rounded border border-yellow-400 bg-yellow-200"></div>
                                                    <span class="text-yellow-800 dark:text-yellow-200"><strong>Yellow:</strong> Quantity < 5 (left border)</span>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                                💡 <strong>Implementation:</strong> Uses column-based conditions with operators (eq, gt, lt), priority levels (1=highest), and user-configurable styling (CSS classes, inline styles, style objects)
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                🏆 <strong>Priority System:</strong> Out of Stock (Priority 1) overrides Low Quantity (Priority 5) - that's why items with 0 stock show red instead of yellow
                                            </div>
                                        </div>
                                        <WidgetRenderer 
                                            :widgets="[conditionalStylingDataTable]" 
                                            @action="handleCustomAction"
                                            @crud-action="handleCrudAction"
                                        />
                                    </div>
                                </div>
                                
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
                                <div v-if="autoModeDataTables.length > 0" class="mb-8">
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

                                <!-- Data Types DataTable -->
                                <div v-if="dataTypesDataTable" class="mt-8">
                                    <h4 class="mb-3 text-base font-medium text-gray-600 dark:text-gray-400">
                                        Data Type Formatting Demo
                                    </h4>
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-200">
                                            <div class="mb-2 font-semibold">Data Type Examples:</div>
                                            <ul class="list-inside list-disc space-y-1">
                                                <li><strong>Currency:</strong> Multiple formats with $ and € symbols</li>
                                                <li><strong>Dates:</strong> Short/Long formats, US/EU cultures</li>
                                                <li><strong>DateTime:</strong> Combined date and time with 12/24 hour formats</li>
                                                <li><strong>Percentage & Numbers:</strong> Decimal place control</li>
                                                <li><strong>Text:</strong> Truncation with ellipsis</li>
                                                <li><strong>Images:</strong> Inline images with controlled width</li>
                                                <li><strong>Widgets:</strong> Embedded knob widgets for ratings</li>
                                            </ul>
                                        </div>
                                        <WidgetRenderer :widgets="[dataTypesDataTable]" />
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

                <!-- DD20250710-1240 - Add Documentation Section for Conditional Styling -->
                <PCard class="mt-8 shadow-lg">
                    <template #header>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">🎨 Conditional Styling Documentation</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-6 p-6">
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    Implementation Guide
                                </h3>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    The DataTable widget now supports conditional row styling based on column values. Configure styles using the <code class="rounded bg-gray-100 px-1 py-0.5 text-xs dark:bg-gray-800">conditionalStyles</code> property:
                                </p>
                                
                                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                    <h4 class="mb-2 font-semibold text-gray-700 dark:text-gray-300">Configuration Example:</h4>
                                    <pre class="overflow-x-auto text-xs text-gray-700 dark:text-gray-300"><code>{
  "conditionalStyles": [
    {
      "column": "inventoryStatus",
      "value": "OUTOFSTOCK", 
      "operator": "eq",
      "priority": 1,
      "styleObject": {
        "backgroundColor": "#fee2e2",
        "color": "#7f1d1d",
        "fontWeight": "bold"
      }
    },
    {
      "column": "quantity",
      "value": 5,
      "operator": "lt",
      "priority": 5,
      "styleObject": {
        "backgroundColor": "#fef3c7",
        "color": "#92400e"
      }
    }
  ]
}</code></pre>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                                        <h4 class="mb-2 font-semibold text-yellow-800 dark:text-yellow-200">Priority System:</h4>
                                        <ul class="list-inside list-disc text-sm text-yellow-700 dark:text-yellow-300">
                                            <li><code>priority: 1</code> - Highest priority</li>
                                            <li><code>priority: 2-998</code> - Medium priority</li>
                                            <li><code>priority: 9999</code> - Default (lowest)</li>
                                            <li>Lower numbers override higher numbers</li>
                                            <li>Rules without priority get 9999</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                        <h4 class="mb-2 font-semibold text-blue-800 dark:text-blue-200">Available Operators:</h4>
                                        <ul class="list-inside list-disc text-sm text-blue-700 dark:text-blue-300">
                                            <li><code>eq</code> - equals</li>
                                            <li><code>ne</code> - not equals</li>
                                            <li><code>lt / lte</code> - less than (or equal)</li>
                                            <li><code>gt / gte</code> - greater than (or equal)</li>
                                            <li><code>contains</code> - string contains</li>
                                            <li><code>startsWith / endsWith</code> - string patterns</li>
                                            <li><code>in / notIn</code> - array membership</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20">
                                        <h4 class="mb-2 font-semibold text-purple-800 dark:text-purple-200">Styling Methods:</h4>
                                        <ul class="list-inside list-disc text-sm text-purple-700 dark:text-purple-300">
                                            <li><code>cssClasses</code> - Apply CSS class names</li>
                                            <li><code>inlineStyles</code> - CSS string format</li>
                                            <li><code>styleObject</code> - JavaScript object format</li>
                                        </ul>
                                        <p class="mt-2 text-xs text-purple-600 dark:text-purple-400">
                                            💡 All three methods can be combined for complex styling
                                        </p>
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