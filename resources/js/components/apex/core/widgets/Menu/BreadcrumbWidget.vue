<!--
File location: resources/js/components/apex/core/widgets/Menu/BreadcrumbWidget.vue

Copyright EXOR Group ltd 2025
Version 1.0.0.0
Widget: APEX Breadcrumb Widget
Description: Breadcrumb provides contextual information about page hierarchy.
Implementing: PrimeVue/Menu/Breadcrumb v.4

Acceptable Parameters:
- widgetId (string, required): Unique identifier for the breadcrumb widget
- items (array): Array of breadcrumb navigation items
  - label (string, required): Display text for the breadcrumb item
  - url (string): URL to navigate to when clicked
  - icon (string): PrimeIcon class or icon identifier
  - disabled (boolean): Whether the breadcrumb item is disabled
- home (object): Home icon configuration
  - icon (string): PrimeIcon class for the home icon
  - url (string): URL for the home link
-->

<script setup lang="ts">
import { computed } from 'vue';

interface BreadcrumbItem {
    label: string;
    url?: string;
    icon?: string;
    disabled?: boolean;
}

interface Props {
    widgetId: string;
    items: BreadcrumbItem[];
    home?: {
        icon?: string;
        url?: string;
    };
}

const props = defineProps<Props>();

// Transform items to PrimeVue breadcrumb model
const breadcrumbModel = computed(() => {
    const items: any[] = [];
    
    // Add home item if configured
    if (props.home) {
        items.push({
            label: '',
            icon: props.home.icon || 'pi pi-home',
            url: props.home.url || '/',
        });
    }
    
    // Add configured items
    props.items.forEach(item => {
        items.push({
            label: item.label,
            url: item.url,
            icon: item.icon,
            disabled: item.disabled,
        });
    });
    
    return items;
});
</script>

<template>
    <div :id="widgetId" class="apex-breadcrumb-widget">
        <PBreadcrumb :model="breadcrumbModel" />
    </div>
</template>

<style scoped>
.apex-breadcrumb-widget {
    margin-bottom: 1rem;
}
</style>