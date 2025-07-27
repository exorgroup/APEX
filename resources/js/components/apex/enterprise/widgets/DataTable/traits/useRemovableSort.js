// resources/js/components/apex/enterprise/widgets/DataTable/traits/useRemovableSort.js

import { computed } from 'vue';

export function useRemovableSort(props) {
    const removableSortConfig = computed(() => ({
        removableSort: props.removableSort ?? true
    }));

    const isRemovableSortEnabled = computed(() => {
        return removableSortConfig.value.removableSort;
    });

    return {
        removableSortConfig,
        isRemovableSortEnabled
    };
}