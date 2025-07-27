// resources/js/components/apex/core/widgets/DataTable/traits/useStripedRows.js

import { computed } from 'vue';

export function useStripedRows(props) {
    const stripedRowsEnabled = computed(() => props.stripedRows ?? true);

    return {
        stripedRowsEnabled
    };
}