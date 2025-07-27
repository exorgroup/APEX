// resources/js/components/apex/core/widgets/DataTable/traits/useDynamicColumns.js

import { ref, computed } from 'vue';

export function useDynamicColumns(props) {
    const visibleColumns = ref([]);

    // Initialize visible columns based on hidden property
    const initVisibleColumns = () => {
        const dataColumns = props.columns.filter(col => !col.hidden);
        visibleColumns.value = [...dataColumns];
    };

    // Handle column toggle
    const onColumnToggle = (val) => {
        visibleColumns.value = val;
    };

    // Computed property for all columns (visible and hidden)
    const allColumns = computed(() => props.columns);

    // Column options for toggle
    const columnOptions = computed(() => {
        return props.columns.filter(col => !col.frozen && !col.hidden);
    });

    return {
        visibleColumns,
        allColumns,
        columnOptions,
        initVisibleColumns,
        onColumnToggle
    };
}