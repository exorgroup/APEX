// resources/js/components/apex/pro/widgets/DataTable/composables/useColumnToggle.js

import { computed } from 'vue';

export function useColumnToggle(props) {
    const columnToggleConfig = computed(() => ({
        columnToggle: props.columnToggle ?? false,
        columnTogglePosition: props.columnTogglePosition ?? 'right'
    }));

    const columnOptions = computed(() => {
        return props.columns.filter(col => !col.frozen && !col.hidden);
    });

    const hasColumnToggle = computed(() => {
        return columnToggleConfig.value.columnToggle && columnOptions.value.length > 0;
    });

    const togglePosition = computed(() => {
        return columnToggleConfig.value.columnTogglePosition;
    });

    return {
        columnToggleConfig,
        columnOptions,
        hasColumnToggle,
        togglePosition
    };
}