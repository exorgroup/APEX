// resources/js/components/apex/pro/widgets/DataTable/traits/usePresort.js

import { computed } from 'vue';

export function usePresort(props) {
    const presortConfig = computed(() => ({
        defaultSortField: props.defaultSortField ?? null,
        defaultSortOrder: props.defaultSortOrder ?? 1,
        multiSortMeta: props.multiSortMeta ?? []
    }));

    const applySorting = () => {
        // Apply initial sorting if configured
        if (presortConfig.value.defaultSortField) {
            return {
                field: presortConfig.value.defaultSortField,
                order: presortConfig.value.defaultSortOrder
            };
        }
        return null;
    };

    return {
        presortConfig,
        applySorting
    };
}