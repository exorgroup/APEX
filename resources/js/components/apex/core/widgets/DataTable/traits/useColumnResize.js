// resources/js/components/apex/core/widgets/DataTable/traits/useColumnResize.js

import { computed } from 'vue';

export function useColumnResize(props) {
    const resizeConfig = computed(() => ({
        resizableColumns: props.resizableColumns ?? false,
        columnResizeMode: props.columnResizeMode ?? 'fit'
    }));

    return {
        resizeConfig
    };
}