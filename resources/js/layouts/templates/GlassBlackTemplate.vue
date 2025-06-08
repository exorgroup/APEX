<script setup lang="ts">
import AppShell from '@/components/AppShell.vue';
import GlassBlackHeader from '@/components/templates/glass-black/GlassBlackHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-800">
        <AppShell variant="header">
            <GlassBlackHeader :breadcrumbs="breadcrumbs" />
            <main class="flex-1">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </AppShell>
    </div>
</template>