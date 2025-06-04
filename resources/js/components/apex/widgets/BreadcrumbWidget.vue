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