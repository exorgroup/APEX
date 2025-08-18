<!--
File location: resources/js/pages/PrimeRegProTest.vue
URL: /primereg-pro-test/
Description: Vue page for testing APEX Pro edition widgets - validates core inheritance and pro-specific features
-->

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import InputTextProWidgetSection from '@/components/PrimeRegTest/InputTextProWidgetSection.vue';

interface Props {
    widgets: any[];
    pageTitle?: string;
    pageDescription?: string;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), {
    pageTitle: 'APEX Pro Widgets Test',
    pageDescription: 'Testing APEX Pro edition widgets with core inheritance and pro features',
    widgets: () => [],
    error: ''
});

// Filter widgets by type and edition
const inputTextProWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'inputtext' && (w.edition === 'pro' || w.props?.edition === 'pro'))
);

// Component statistics for debugging
const widgetStats = computed(() => ({
    total: props.widgets.length,
    inputTextPro: inputTextProWidgets.value.length,
    coreInherited: inputTextProWidgets.value.slice(0, 10).length,
    proSpecific: inputTextProWidgets.value.slice(10).length
}));

// Development mode check
const isDevelopment = computed(() => {
    try {
        return import.meta.env.DEV;
    } catch (error) {
        return false;
    }
});

// Development mode logging
onMounted(() => {
    try {
        console.log('=== PrimeRegProTest Debug ===');
        console.log('Raw props.widgets:', props.widgets);
        console.log('Raw widgets length:', props.widgets.length);
        console.log('InputText Pro widgets:', inputTextProWidgets.value);
        console.log('InputText Pro widgets length:', inputTextProWidgets.value.length);
        console.log('Widget stats:', widgetStats.value);
        console.log('========================');
        
        if (isDevelopment.value) {
            console.log('PrimeRegProTest mounted with stats:', widgetStats.value);
            console.log('Pro widgets:', inputTextProWidgets.value);
        }
    } catch (error) {
        console.error('Error in PrimeRegProTest onMounted:', error);
    }
});
</script>

<template>
    <div class="max-w-6xl mx-auto p-6">
        <Head :title="pageTitle" />
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ pageTitle }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ pageDescription }}</p>
            
            <!-- Pro Edition Indicator -->
            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                Pro Edition Testing v1
            </div>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <i class="pi pi-exclamation-triangle mr-2"></i>
            {{ error }}
        </div>

        <!-- No Widgets Message -->
        <div v-if="widgets.length === 0" class="text-center py-12 text-gray-500">
            <i class="pi pi-inbox text-4xl mb-4"></i>
            <p>No Pro edition widgets available for testing</p>
        </div>

        <!-- Widget Statistics (Development Mode) -->
        <div v-if="isDevelopment && widgets.length > 0" class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Development Stats</h3>
            <div class="text-xs text-blue-700 dark:text-blue-300 grid grid-cols-2 md:grid-cols-4 gap-2">
                <div>Total: {{ widgetStats.total }}</div>
                <div>Pro InputText: {{ widgetStats.inputTextPro }}</div>
                <div>Core Inherited: {{ widgetStats.coreInherited }}</div>
                <div>Pro Specific: {{ widgetStats.proSpecific }}</div>
            </div>
        </div>

        <!-- Widget Sections -->
        <div v-else class="space-y-12">
            <!-- InputText Pro Widgets Section -->
            <InputTextProWidgetSection :widgets="inputTextProWidgets" />

            <!-- Future Pro Widget Sections -->
            <!-- Add more pro widget sections here as they are created -->
            <!-- <TextareaProWidgetSection :widgets="textareaProWidgets" /> -->
            <!-- <SelectProWidgetSection :widgets="selectProWidgets" /> -->
        </div>

        <!-- Testing Instructions -->
        <div class="mt-12 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Testing Instructions</h3>
            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                <div>
                    <span class="font-medium text-blue-600">Core Inheritance:</span> 
                    Verify that all core features work correctly in Pro edition widgets.
                </div>
                <div>
                    <span class="font-medium text-purple-600">Pro Events:</span> 
                    Open browser console to see Pro event handling (blur, focus, clipboard, etc.).
                </div>
                <div>
                    <span class="font-medium text-green-600">Pro Features:</span> 
                    Test advanced validation, state management, and server integration.
                </div>
                <div>
                    <span class="font-medium text-orange-600">Error Handling:</span> 
                    Pro widgets should show enhanced error displays and recovery options.
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Pro edition specific styling */
.pro-indicator {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>