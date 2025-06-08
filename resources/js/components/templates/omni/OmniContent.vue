<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import UserMenuContent from '@/components/UserMenuContent.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Menu, Bell, Sparkles } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

// Define the background pattern as a computed property to avoid inline quote issues
const backgroundPattern = computed(() => {
    return `url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fbbf24' fill-opacity='0.3'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E")`;
});
</script>

<template>
    <div class="ml-64 flex flex-col">
        <!-- Top Header with gradient -->
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-lg border-b-2 border-yellow-200 dark:bg-gray-900/90 dark:border-yellow-900/30">
            <div class="flex h-16 items-center px-6">
                <!-- Mobile menu button -->
                <Button variant="ghost" size="icon" class="lg:hidden">
                    <Menu class="h-5 w-5" />
                </Button>

                <!-- Breadcrumbs with custom styling -->
                <div class="flex-1">
                    <Breadcrumbs v-if="breadcrumbs.length > 0" :breadcrumbs="breadcrumbs" class="text-amber-700 dark:text-yellow-400" />
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-x-4">
                    <!-- Special Action Button -->
                    <Button variant="ghost" size="icon" class="relative group">
                        <Sparkles class="h-5 w-5 text-yellow-600 group-hover:text-yellow-500 transition-colors" />
                    </Button>

                    <!-- Notifications with animation -->
                    <Button variant="ghost" size="icon" class="relative">
                        <Bell class="h-5 w-5" />
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                    </Button>

                    <!-- User Menu with gradient avatar -->
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="relative size-10 rounded-full hover:ring-2 hover:ring-yellow-400 transition-all">
                                <Avatar class="size-8">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white font-bold">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56 border-yellow-200 dark:border-yellow-900/30">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </header>

        <!-- Main Content with subtle pattern -->
        <main class="flex-1 p-6 relative">
            <div class="absolute inset-0 opacity-5 omni-pattern"></div>
            <div class="relative z-10">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
.omni-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fbbf24' fill-opacity='0.3'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
}
</style>