// resources/js/components/apex/pro/widgets/DataTable/composables/useBasicFilter.js

import { ref, computed } from 'vue';

export function useBasicFilter(props) {
    const filters = ref({});

    const filterConfig = computed(() => ({
        columnFilters: props.columnFilters ?? false,
        filterMode: props.filterMode ?? 'lenient',
        filterDisplay: props.filterDisplay ?? 'menu'
    }));

    const onFilter = (event) => {
        filters.value = event.filters;
        // Handle filtering logic
        console.log('Filters applied:', filters.value);
    };

    const clearFilters = () => {
        filters.value = {};
    };

    const hasActiveFilters = computed(() => {
        return Object.keys(filters.value).length > 0;
    });

    return {
        filterConfig,
        filters,
        onFilter,
        clearFilters,
        hasActiveFilters
    };
}