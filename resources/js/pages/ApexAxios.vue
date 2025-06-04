<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import WidgetRenderer from '@/components/apex/WidgetRenderer.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

interface Props {
    widgets: any[];
    rawConfig: any[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'APEX Widget Test',
        href: '/apex',
    },
];

// Dynamic widget configuration for testing
const dynamicConfig = ref(`[
    {
        "type": "breadcrumb",
        "items": [
            {"label": "Dynamic", "url": "/dynamic"},
            {"label": "Breadcrumb", "url": "/breadcrumb"},
            {"label": "Test"}
        ]
    }
]`);

const dynamicWidgets = ref<any[]>([]);
const error = ref<string>('');
const loading = ref(false);

const loadDynamicWidgets = async () => {
    try {
        loading.value = true;
        error.value = '';
        
        const config = JSON.parse(dynamicConfig.value);
        
        // Using axios - much cleaner!
        const { data } = await axios.post(route('apex.dynamic-test'), {
            widgets: config
        });
        
        dynamicWidgets.value = data.widgets;
        
    } catch (e: any) {
        if (e.response) {
            // Server error with response
            error.value = `Server Error (${e.response.status}): ${e.response.data.message || e.response.statusText}`;
        } else if (e.request) {
            // Request made but no response
            error.value = 'No response from server. Please check your connection.';
        } else if (e instanceof SyntaxError) {
            // JSON parse error
            error.value = 'Invalid JSON format. Please check your configuration.';
        } else {
            // Something else
            error.value = e.message || 'An unknown error occurred';
        }
        dynamicWidgets.value = [];
    } finally {
        loading.value = false;
    }
};

// Debug: Log props when component mounts
onMounted(() => {
    console.log('ApexTest mounted with props:', {
        widgets: props.widgets,
        rawConfig: props.rawConfig
    });
});
</script>

<template>
    <Head title="APEX Widget Test" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="mx-auto max-w-6xl space-y-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    APEX JSON Widget System Test Axios
                </h1>

                <!-- Static Widgets Section -->
                <PCard>
                    <template #header>
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Static Widget Rendering</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-4 p-6">
                            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                These widgets are rendered from server-side JSON configuration:
                            </p>
                            
                            <WidgetRenderer :widgets="widgets" />
                            
                            <details class="mt-6">
                                <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                                    View Raw Configuration
                                </summary>
                                <pre class="mt-2 overflow-x-auto rounded bg-gray-100 p-4 text-xs dark:bg-gray-800">{{ JSON.stringify(rawConfig, null, 2) }}</pre>
                            </details>
                        </div>
                    </template>
                </PCard>

                <!-- Dynamic Widget Test Section -->
                <PCard>
                    <template #header>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Dynamic Widget Test</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="space-y-4 p-6">
                            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                Edit the JSON configuration below and click "Render" to test dynamic widget creation:
                            </p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Widget Configuration (JSON)
                                    </label>
                                    <textarea
                                        v-model="dynamicConfig"
                                        class="h-32 w-full rounded-md border border-gray-300 p-3 font-mono text-sm dark:border-gray-600 dark:bg-gray-800"
                                        :disabled="loading"
                                    />
                                </div>
                                
                                <PButton
                                    @click="loadDynamicWidgets"
                                    :label="loading ? 'Rendering...' : 'Render Widgets'"
                                    :icon="loading ? 'pi pi-spin pi-spinner' : 'pi pi-play'"
                                    severity="success"
                                    :disabled="loading"
                                />
                                
                                <div v-if="error" class="rounded-md bg-red-50 p-4 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">
                                    Error: {{ error }}
                                </div>
                                
                                <div v-if="dynamicWidgets.length > 0" class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <h3 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Rendered Result:</h3>
                                    <WidgetRenderer :widgets="dynamicWidgets" />
                                </div>
                            </div>
                        </div>
                    </template>
                </PCard>

                <!-- Widget Registry Info -->
                <PCard>
                    <template #header>
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Widget System Information</h2>
                        </div>
                    </template>
                    
                    <template #content>
                        <div class="p-6">
                            <h3 class="mb-3 font-semibold">Registered Widget Types:</h3>
                            <ul class="list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li>breadcrumb - PrimeVue Breadcrumb Widget</li>
                                <li class="italic text-gray-400">More widgets can be easily added...</li>
                            </ul>
                            
                            <h3 class="mb-3 mt-6 font-semibold">Key Features:</h3>
                            <ul class="list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li>Automatic ID generation if not provided</li>
                                <li>JSON-driven configuration</li>
                                <li>Type-safe widget contracts</li>
                                <li>Extensible widget registry</li>
                                <li>Server and client-side rendering support</li>
                            </ul>
                        </div>
                    </template>
                </PCard>
            </div>
        </div>
    </AppLayout>
</template>