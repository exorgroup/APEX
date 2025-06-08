<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import LaravelTemplate from '@/layouts/templates/LaravelTemplate.vue';
import GlassWhiteTemplate from '@/layouts/templates/GlassWhiteTemplate.vue';
import GlassBlackTemplate from '@/layouts/templates/GlassBlackTemplate.vue';
import MaterialTemplate from '@/layouts/templates/MaterialTemplate.vue';
import OmniTemplate from '@/layouts/templates/OmniTemplate.vue';
import type { BreadcrumbItemType, SharedData } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<SharedData>();

// Get current template from shared data
const currentTemplate = computed(() => page.props.template?.current || 'laravel');

// Map template names to components
const templateComponents = {
    'laravel': LaravelTemplate,
    'glass-white': GlassWhiteTemplate,
    'glass-black': GlassBlackTemplate,
    'material': MaterialTemplate,
    'omni': OmniTemplate,
};

// Get the current template component
const TemplateComponent = computed(() => {
    return templateComponents[currentTemplate.value as keyof typeof templateComponents] || LaravelTemplate;
});
</script>

<template>
    <component :is="TemplateComponent" :breadcrumbs="breadcrumbs">
        <slot />
    </component>
</template>