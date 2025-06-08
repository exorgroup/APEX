<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import OmniSidebar from '@/components/templates/omni/OmniSidebar.vue';
import OmniContent from '@/components/templates/omni/OmniContent.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-yellow-50 via-amber-50 to-orange-50 dark:from-gray-900 dark:via-yellow-950/10 dark:to-gray-900">
        <AppShell variant="sidebar">
            <OmniSidebar />
            <OmniContent :breadcrumbs="breadcrumbs">
                <slot />
            </OmniContent>
        </AppShell>
    </div>
</template>