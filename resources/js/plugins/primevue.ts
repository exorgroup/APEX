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
import Image from 'primevue/image';

import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import Dialog from 'primevue/dialog';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Divider from 'primevue/divider';

import MultiSelect from 'primevue/multiselect';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import Menu from 'primevue/menu';

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

        app.use(ToastService);
        app.component('PToast', Toast);
        app.component('PDialog', Dialog);

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
        app.component('PImage', Image);
        
        app.component('PDataTable', DataTable);
        app.component('PColumn', Column);
        app.component('PColumnGroup', ColumnGroup);
        app.component('PRow', Row);

        app.component('PMultiSelect', MultiSelect);
        app.component('PCalendar', Calendar);
        app.component('PDropdown', Dropdown);
        app.component('PDivider', Divider);
        app.component('PMenu', Menu);
    }
};