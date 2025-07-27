// resources/js/components/apex/core/widgets/DataTable/traits/usePagination.js

import { ref, computed } from 'vue';

export function usePagination(props) {
    const first = ref(0);

    const paginationConfig = computed(() => ({
        paginator: props.paginator ?? true,
        paginatorPosition: props.paginatorPosition ?? 'bottom',
        rows: props.rows ?? 10,
        rowsPerPageOptions: props.rowsPerPageOptions ?? [5, 10, 25, 50, 100]
    }));

    const onPage = (event) => {
        first.value = event.first;
        // Emit event for lazy loading if needed
    };

    return {
        first,
        paginationConfig,
        onPage
    };
}