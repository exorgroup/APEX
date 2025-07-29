<!--
File location: resources/js/components/apex/pro/widgets/Menu/BreadcrumbWidget.vue

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
- separator (string): Custom separator string between breadcrumb items
- pointer (boolean): Enable cursor pointer styling for breadcrumb items
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
    separator?: string;
    pointer?: boolean;
}

const props = defineProps<Props>();

// Transform items to PrimeVue breadcrumb model
const breadcrumbModel = computed(() => {
    // Only add configured items (home is handled by PBreadcrumb :home prop)
    return props.items.map(item => ({
        label: item.label,
        url: item.url,
        icon: item.icon,
        disabled: item.disabled,
    }));
});

// Determine if pointer styling should be applied
const itemClass = computed(() => {
    return props.pointer ? 'cursor-pointer' : '';
});
</script>

<template>
    <div :id="widgetId" class="apex-breadcrumb-widget">
        <PBreadcrumb :home="home" :model="breadcrumbModel">
            <template v-if="pointer || items.some(item => item.icon)" #item="{ item }">
                <a :class="itemClass" :href="item.url">
                    <span v-if="item.icon" :class="item.icon"></span>
                    <span v-if="item.icon && '&nbsp;' && item.label"> </span>
                    <span>{{ item.label }}</span>
                </a>
            </template>
            <template v-if="separator" #separator>{{ separator }}</template>
        </PBreadcrumb>
    </div>
</template>

<style scoped>
.apex-breadcrumb-widget {
    margin-bottom: 1rem;
}
</style>