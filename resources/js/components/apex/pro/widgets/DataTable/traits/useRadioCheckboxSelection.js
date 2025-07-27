// resources/js/components/apex/pro/widgets/DataTable/traits/useRadioCheckboxSelection.js

import { computed } from 'vue';

export function useRadioCheckboxSelection(props) {
    const checkboxConfig = computed(() => {
        const selectionMode = props.selectionMode;
        const showCheckboxColumn = (selectionMode === 'checkbox') || 
                                    (selectionMode === 'multiple' && (props.selectAll ?? false));

        return {
            selectionMode,
            selectAll: props.selectAll ?? false,
            showCheckboxColumn
        };
    });

    const isCheckboxMode = computed(() => checkboxConfig.value.selectionMode === 'checkbox');
    const isMultipleMode = computed(() => checkboxConfig.value.selectionMode === 'multiple');

    return {
        checkboxConfig,
        isCheckboxMode,
        isMultipleMode
    };
}