import type { App } from 'vue';
import PrimeVue from 'primevue/config';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber'; 
import Knob from 'primevue/knob';
import Card from 'primevue/card';
import Breadcrumb from 'primevue/breadcrumb';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Checkbox from 'primevue/checkbox';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

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
        app.component('PInputNumber', InputNumber);
        app.component('PKnob', Knob);
        app.component('PCard', Card);
        app.component('PBreadcrumb', Breadcrumb);
        app.component('PDatePicker', DatePicker);
        app.component('PSelect', Select);
        app.component('PCheckbox', Checkbox);
        app.component('PDataTable', DataTable);
        app.component('PColumn', Column);
    }
};