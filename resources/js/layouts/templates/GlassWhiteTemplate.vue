<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import GlassWhiteHeader from '@/components/templates/glass-white/GlassWhiteHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <AppShell variant="header">
            <GlassWhiteHeader :breadcrumbs="breadcrumbs" />
            <main class="flex-1">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </AppShell>
    </div>
</template>