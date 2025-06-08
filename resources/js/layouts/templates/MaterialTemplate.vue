<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import MaterialSidebar from '@/components/templates/material/MaterialSidebar.vue';
import MaterialContent from '@/components/templates/material/MaterialContent.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-50 dark:from-gray-900 dark:via-purple-950/20 dark:to-gray-900">
        <AppShell variant="sidebar">
            <MaterialSidebar />
            <MaterialContent :breadcrumbs="breadcrumbs">
                <slot />
            </MaterialContent>
        </AppShell>
    </div>
</template>