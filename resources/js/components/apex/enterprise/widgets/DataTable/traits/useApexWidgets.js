// resources/js/components/apex/enterprise/widgets/DataTable/traits/useApexWidgets.js

import { computed, defineAsyncComponent } from 'vue';

export function useApexWidgets(props) {
    const apexWidgetConfig = computed(() => ({
        enabled: props.columns.some(col => col.dataType === 'apexwidget')
    }));

    // Get widget component for ApexWidget type
    const getWidgetComponent = (type) => {
        // Import widget components as needed
        const widgetMap = {
            'knob': defineAsyncComponent(() => import('../../../widgets/KnobWidget.vue')),
            'datepicker': defineAsyncComponent(() => import('../../../widgets/DatePickerWidget.vue')),
            'inputtext': defineAsyncComponent(() => import('../../../widgets/InputTextWidget.vue')),
            'inputnumber': defineAsyncComponent(() => import('../../../widgets/InputNumberWidget.vue')),
            'checkbox': defineAsyncComponent(() => import('../../../widgets/CheckboxWidget.vue')),
            'button': defineAsyncComponent(() => import('../../../widgets/ButtonWidget.vue')),
            'rating': defineAsyncComponent(() => import('../../../widgets/RatingWidget.vue')),
            'slider': defineAsyncComponent(() => import('../../../widgets/SliderWidget.vue')),
            'toggle': defineAsyncComponent(() => import('../../../widgets/ToggleWidget.vue')),
            'badge': defineAsyncComponent(() => import('../../../widgets/BadgeWidget.vue')),
            'chip': defineAsyncComponent(() => import('../../../widgets/ChipWidget.vue')),
            'avatar': defineAsyncComponent(() => import('../../../widgets/AvatarWidget.vue')),
            'progressbar': defineAsyncComponent(() => import('../../../widgets/ProgressBarWidget.vue')),
            'tag': defineAsyncComponent(() => import('../../../widgets/TagWidget.vue')),
            // Add more widgets as needed
        };
        
        return widgetMap[type] || null;
    };

    const renderApexWidget = (column, value) => {
        if (column.dataType !== 'apexwidget' || !column.widgetConfig) {
            return null;
        }

        const component = getWidgetComponent(column.widgetConfig.type);
        if (!component) {
            console.warn(`ApexWidget type "${column.widgetConfig.type}" not found`);
            return null;
        }

        return {
            component,
            props: {
                ...column.widgetConfig,
                value: value
            }
        };
    };

    const hasApexWidgets = computed(() => {
        return props.columns.some(col => col.dataType === 'apexwidget' && col.widgetConfig);
    });

    const getApexWidgetColumns = computed(() => {
        return props.columns.filter(col => col.dataType === 'apexwidget' && col.widgetConfig);
    });

    return {
        apexWidgetConfig,
        getWidgetComponent,
        renderApexWidget,
        hasApexWidgets,
        getApexWidgetColumns
    };
}