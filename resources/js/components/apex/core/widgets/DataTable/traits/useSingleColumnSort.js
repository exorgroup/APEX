// resources/js/components/apex/core/widgets/DataTable/traits/useSingleColumnSort.js

import { ref, computed } from 'vue';

export function useSingleColumnSort(props) {
    const sortField = ref(undefined);
    const sortOrder = ref(undefined);

    const sortConfig = computed(() => ({
        sortMode: 'single', // Core only supports single
        defaultSortOrder: props.defaultSortOrder ?? 1
    }));

    const onSort = (event) => {
        sortField.value = event.sortField;
        sortOrder.value = event.sortOrder;
        // Emit event for lazy loading if needed
    };

    return {
        sortConfig,
        sortField,
        sortOrder,
        onSort
    };
}