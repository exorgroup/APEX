<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { getInitials } from '@/composables/useInitials';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    LayoutGrid, 
    Users, 
    Settings, 
    FileText, 
    BarChart,
    Package,
    Calendar,
    Mail
} from 'lucide-vue-next';

const page = usePage();
const auth = computed(() => page.props.auth);

interface NavItem {
    title: string;
    href: string;
    icon?: any;
}

const menuItems: NavItem[] = [
    { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
    { title: 'Projects', href: '/projects', icon: Package },
    { title: 'Users', href: '/users', icon: Users },
    { title: 'Reports', href: '/reports', icon: BarChart },
    { title: 'Documents', href: '/documents', icon: FileText },
    { title: 'Calendar', href: '/calendar', icon: Calendar },
    { title: 'Messages', href: '/messages', icon: Mail },
    { title: 'Settings', href: '/settings', icon: Settings },
];

const isActive = (href: string) => {
    return page.url === href || page.url.startsWith(href + '/');
};
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gradient-to-b from-purple-600 to-purple-800 dark:from-purple-900 dark:to-purple-950">
        <!-- Sidebar Header -->
        <div class="flex h-16 items-center px-6">
            <AppLogo class="text-white" />
        </div>

        <!-- User Profile Section -->
        <div class="mx-4 mb-6 rounded-xl bg-white/10 p-4 backdrop-blur">
            <div class="flex items-center space-x-3">
                <Avatar class="h-12 w-12 ring-2 ring-white/50">
                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                    <AvatarFallback class="bg-purple-500 text-white">
                        {{ getInitials(auth.user?.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex-1 truncate">
                    <p class="text-sm font-medium text-white">{{ auth.user.name }}</p>
                    <p class="text-xs text-purple-200">{{ auth.user.email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1 px-4 pb-4">
            <Link
                v-for="item in menuItems"
                :key="item.href"
                :href="item.href"
                class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-white transition-all hover:bg-white/10"
                :class="{ 'bg-white/20': isActive(item.href) }"
            >
                <component 
                    v-if="item.icon" 
                    :is="item.icon" 
                    class="h-5 w-5 flex-shrink-0" 
                />
                <span>{{ item.title }}</span>
            </Link>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4">
            <Button class="w-full bg-white/20 text-white hover:bg-white/30">
                Settings
            </Button>
        </div>
    </aside>
</template>