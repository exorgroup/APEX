import type { App } from 'vue';
import PrimeVue from 'primevue/config';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Knob from 'primevue/knob';
import Card from 'primevue/card';
import Aura from '@primeuix/themes/aura';

export default {
    install(app: App) {
        app.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    prefix: 'p',
                    darkModeSelector: '.dark',
                    cssLayer: false
                }
            }
        });

        // Register PrimeVue components globally
        app.component('PButton', Button);
        app.component('PInputText', InputText);
        app.component('PKnob', Knob);
        app.component('PCard', Card);
    }
};