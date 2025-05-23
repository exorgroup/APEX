import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Import PrimeVue and theme
import PrimeVue from 'primevue/config';
import Lara from '@primeuix/themes/lara'; // You can use Aura, Lara, etc.

// Import components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

// Import icons
import 'primeicons/primeicons.css';

// Import APEX Event Bus
import './apex/apex-events.js';

createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        
        // Configure PrimeVue exactly as documentation shows
        app.use(PrimeVue, {
            theme: {
                preset: Lara
            }
        });
        
        // Register components
        app.component('Button', Button);
        app.component('InputText', InputText);
        
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});