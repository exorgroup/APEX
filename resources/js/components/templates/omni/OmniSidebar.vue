<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    Zap,
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
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gradient-to-b from-amber-400 via-yellow-400 to-orange-400 dark:from-gray-800 dark:to-gray-900">
        <!-- Sidebar Header with animated background -->
        <div class="relative flex h-16 items-center px-6 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-300/50 to-orange-300/50 animate-pulse"></div>
            <AppLogo class="relative z-10 text-gray-900 dark:text-white" />
            <Zap class="ml-2 h-5 w-5 text-gray-900 dark:text-yellow-400 animate-bounce" />
        </div>

        <!-- User Profile Card -->
        <div class="mx-4 mb-6 rounded-2xl bg-white/90 dark:bg-gray-800/90 p-4 shadow-lg backdrop-blur">
            <div class="flex items-center space-x-3">
                <Avatar class="h-12 w-12 ring-4 ring-yellow-300 dark:ring-yellow-600">
                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                    <AvatarFallback class="bg-gradient-to-br from-yellow-400 to-orange-400 text-white font-bold">
                        {{ getInitials(auth.user?.name) }}
                    </AvatarFallback>
                </Avatar>
                <div class="flex-1 truncate">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth.user.name }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ auth.user.email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation with custom styling -->
        <nav class="flex-1 space-y-1 px-4 pb-4">
            <Link
                v-for="item in menuItems"
                :key="item.href"
                :href="item.href"
                class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-300 transition-all hover:bg-white/50 dark:hover:bg-gray-800/50"
                :class="isActive(item.href) ? 'bg-white/70 text-gray-900 shadow-md scale-105 dark:bg-gray-800 dark:text-yellow-400' : ''"
            >
                <component 
                    v-if="item.icon" 
                    :is="item.icon" 
                    class="h-5 w-5 flex-shrink-0" 
                />
                <span>{{ item.title }}</span>
            </Link>
        </nav>

        <!-- Animated Footer -->
        <div class="p-4 border-t border-yellow-300/50 dark:border-gray-700">
            <div class="rounded-xl bg-gradient-to-r from-orange-400 to-yellow-400 dark:from-yellow-600 dark:to-orange-600 p-3 text-center">
                <p class="text-xs font-bold text-white">Powered by Omni</p>
            </div>
        </div>
    </aside>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>