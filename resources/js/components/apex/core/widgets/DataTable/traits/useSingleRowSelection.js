// resources/js/components/apex/core/widgets/DataTable/traits/useSingleRowSelection.js

import { ref, computed } from 'vue';

export function useSingleRowSelection(props) {
    const selectedItems = ref([...props.selection || []]);
    
    const selectionConfig = computed(() => ({
        selectionMode: props.selectionMode === 'single' ? 'single' : null,
        metaKeySelection: props.metaKeySelection ?? true
    }));

    return {
        selectionConfig,
        selectedItems
    };
}