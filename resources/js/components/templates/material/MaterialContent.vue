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
import { Menu, Bell } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
</script>

<template>
    <div class="ml-64 flex flex-col">
        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-purple-100 dark:bg-gray-900/80 dark:border-purple-900/30">
            <div class="flex h-16 items-center px-6">
                <!-- Mobile menu button -->
                <Button variant="ghost" size="icon" class="lg:hidden">
                    <Menu class="h-5 w-5" />
                </Button>

                <!-- Breadcrumbs -->
                <div class="flex-1">
                    <Breadcrumbs v-if="breadcrumbs.length > 0" :breadcrumbs="breadcrumbs" />
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-x-4">
                    <!-- Notifications -->
                    <Button variant="ghost" size="icon" class="relative">
                        <Bell class="h-5 w-5" />
                        <span class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-purple-600"></span>
                    </Button>

                    <!-- User Menu -->
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="relative size-10 rounded-full">
                                <Avatar class="size-8">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="bg-purple-600 text-white">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <slot />
        </main>
    </div>
</template>