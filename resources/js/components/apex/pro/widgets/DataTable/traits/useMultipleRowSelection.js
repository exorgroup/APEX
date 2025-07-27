// resources/js/components/apex/pro/widgets/DataTable/traits/useMultipleRowSelection.js

import { ref, computed } from 'vue';

export function useMultipleRowSelection(props) {
    const selectedItems = ref([...props.selection || []]);
    
    const multipleSelectionConfig = computed(() => ({
        selectionMode: props.selectionMode || undefined,
        metaKeySelection: props.metaKeySelection ?? true,
        selectAll: props.selectAll ?? false
    }));

    const hasSelectedItems = computed(() => selectedItems.value.length > 0);
    const selectedCount = computed(() => selectedItems.value.length);

    return {
        multipleSelectionConfig,
        selectedItems,
        hasSelectedItems,
        selectedCount
    };
}