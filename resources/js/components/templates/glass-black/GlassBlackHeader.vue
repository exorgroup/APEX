<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
} from '@/components/ui/navigation-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Menu } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(() => (url: string) => page.url === url);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];
</script>

<template>
    <div class="sticky top-0 z-50 w-full">
        <!-- Glass effect header with black transparency -->
        <div class="backdrop-blur-xl bg-black/40 border-b border-white/10 shadow-2xl">
            <!-- Main Navigation Bar -->
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Logo -->
                <div class="flex items-center gap-x-4">
                    <Link :href="route('dashboard')" class="flex items-center gap-x-2">
                        <AppLogo class="text-white" />
                    </Link>

                    <!-- Desktop Navigation -->
                    <NavigationMenu class="hidden md:flex">
                        <NavigationMenuList>
                            <NavigationMenuItem v-for="(item, index) in mainNavItems" :key="index">
                                <Link :href="item.href">
                                    <NavigationMenuLink
                                        class="inline-flex h-10 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-gray-200 transition-all hover:bg-white/10 hover:text-white focus:outline-none disabled:pointer-events-none disabled:opacity-50"
                                        :class="isCurrentRoute(item.href) ? 'bg-white/20 text-white' : ''"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="mr-2 h-4 w-4" />
                                        {{ item.title }}
                                    </NavigationMenuLink>
                                </Link>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <!-- Mobile Menu Button -->
                <Sheet>
                    <SheetTrigger :as-child="true" class="md:hidden">
                        <Button variant="ghost" size="icon" class="ml-auto mr-2 h-9 w-9 text-gray-200 hover:bg-white/10 hover:text-white">
                            <Menu class="h-5 w-5" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="w-[300px] p-6 backdrop-blur-xl bg-black/90 text-white border-gray-800">
                        <SheetTitle class="text-white">Navigation Menu</SheetTitle>
                        <SheetHeader class="mb-4">
                            <AppLogo class="text-white" />
                        </SheetHeader>
                        <nav class="space-y-1">
                            <Link
                                v-for="item in mainNavItems"
                                :key="item.title"
                                :href="item.href"
                                class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white"
                                :class="isCurrentRoute(item.href) ? 'bg-white/20 text-white' : ''"
                            >
                                <component v-if="item.icon" :is="item.icon" class="h-5 w-5" />
                                {{ item.title }}
                            </Link>
                        </nav>
                    </SheetContent>
                </Sheet>

                <!-- Right Side Actions -->
                <div class="ml-auto flex items-center gap-x-4">
                    <!-- User Menu -->
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 text-gray-200 hover:bg-white/10"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="rounded-lg bg-white/20 font-semibold text-white">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56 backdrop-blur-xl bg-black/90 text-white border-gray-800">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Breadcrumbs Bar -->
            <div v-if="props.breadcrumbs.length > 0" class="border-t border-white/10">
                <div class="mx-auto flex h-12 items-center px-4 md:max-w-7xl">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" class="text-gray-300" />
                </div>
            </div>
        </div>
    </div>
</template>