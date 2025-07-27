// resources/js/components/apex/pro/widgets/DataTable/traits/useMultipleColumnsSort.js

import { ref, computed } from 'vue';

export function useMultipleColumnsSort(props) {
    const multiSortMeta = ref(props.multiSortMeta || []);

    const multiSortConfig = computed(() => ({
        sortMode: props.sortMode ?? 'single',
        removableSort: props.removableSort ?? true
    }));

    const onMultiSort = (event) => {
        multiSortMeta.value = event.multiSortMeta || [];
        // Emit event for lazy loading if needed
    };

    return {
        multiSortConfig,
        multiSortMeta,
        onMultiSort
    };
}