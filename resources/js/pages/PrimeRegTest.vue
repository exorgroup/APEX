<!--
File location: resources/js/pages/PrimeRegTest.vue
Description: Simple Vue page for testing APEX Core widgets using WidgetRenderer
-->

<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import InputTextWidgetSection from '@/components/PrimeRegTest/InputTextWidgetSection.vue';

interface Props {
    widgets: any[];
    pageTitle?: string;
    pageDescription?: string;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), {
    pageTitle: 'APEX Core Widgets Test',
    pageDescription: 'Testing APEX Core widgets with WidgetRenderer',
    widgets: () => [],
    error: ''
});

// Filter widgets by type like the knob example
const inputTextWidgets = computed(() => 
    props.widgets.filter(w => w.type === 'inputtext')
);
</script>

<template>
    <div class="max-w-6xl mx-auto p-6">
        <Head :title="pageTitle" />
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ pageTitle }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ pageDescription }}</p>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <i class="pi pi-exclamation-triangle mr-2"></i>
            {{ error }}
        </div>

        <!-- No Widgets Message -->
        <div v-if="widgets.length === 0" class="text-center py-12 text-gray-500">
            <i class="pi pi-inbox text-4xl mb-4"></i>
            <p>No widgets available for testing</p>
        </div>

        <!-- Widget Sections -->
        <div v-else class="space-y-12">
            <!-- InputText Widgets Section -->
            <InputTextWidgetSection :widgets="inputTextWidgets" />
        </div>
    </div>
</template>