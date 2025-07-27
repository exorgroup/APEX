// resources/js/components/apex/core/widgets/DataTable/traits/useSearch.js

import { ref, computed } from 'vue';

export function useSearch(props) {
    const globalFilterValue = ref('');

    const searchConfig = computed(() => ({
        globalFilter: props.globalFilter ?? false,
        globalFilterFields: props.globalFilterFields ?? []
    }));

    const onGlobalFilter = () => {
        // For client-side mode, the computed property will handle filtering
        // For server-side mode, reload data with filter
        console.log('Global filter changed:', globalFilterValue.value);
    };

    return {
        searchConfig,
        globalFilterValue,
        onGlobalFilter
    };
}