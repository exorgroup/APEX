// resources/js/components/apex/pro/widgets/DataTable/composables/useGroupActions.js

import { computed } from 'vue';

export function useGroupActions(props, selectedItems, emit) {
    const groupActionsConfig = computed(() => ({
        groupActions: props.groupActions ?? []
    }));

    const hasGroupActions = computed(() => {
        return groupActionsConfig.value.groupActions.length > 0;
    });

    const hasSelectedItems = computed(() => {
        return selectedItems.value.length > 0;
    });

    const selectedCount = computed(() => {
        return selectedItems.value.length;
    });

    const executeGroupAction = (action) => {
        if (action.confirm) {
            if (confirm(action.confirmMessage || `Are you sure you want to ${action.label.toLowerCase()}?`)) {
                performGroupAction(action);
            }
        } else {
            performGroupAction(action);
        }
    };

    const performGroupAction = (action) => {
        console.log(`Executing ${action.action} on ${selectedCount.value} items:`, selectedItems.value);
        
        // Emit event for parent to handle
        emit('group-action', {
            action: action.action,
            items: [...selectedItems.value],
            count: selectedCount.value,
            actionData: action
        });
    };

    const clearSelection = () => {
        selectedItems.value = [];
    };

    return {
        groupActionsConfig,
        hasGroupActions,
        hasSelectedItems,
        selectedCount,
        executeGroupAction,
        performGroupAction,
        clearSelection
    };
}