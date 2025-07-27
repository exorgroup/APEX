// resources/js/components/apex/enterprise/widgets/DataTable/composables/useGroupActions.js

import { computed } from 'vue';

export function useGroupActions(props, selectedItems, emit) {
    const enterpriseGroupActionsConfig = computed(() => ({
        groupActions: props.groupActions ?? [],
        bulkOperations: {
            maxItems: props.groupActionSettings?.maxItems ?? 1000,
            confirmThreshold: props.groupActionSettings?.confirmThreshold ?? 10,
            showProgress: props.groupActionSettings?.showProgress ?? true,
            allowCancel: props.groupActionSettings?.allowCancel ?? true
        }
    }));

    const hasGroupActions = computed(() => {
        return enterpriseGroupActionsConfig.value.groupActions.length > 0;
    });

    const hasSelectedItems = computed(() => {
        return selectedItems.value.length > 0;
    });

    const selectedCount = computed(() => {
        return selectedItems.value.length;
    });

    const executeGroupAction = async (action) => {
        const config = enterpriseGroupActionsConfig.value.bulkOperations;
        const count = selectedCount.value;

        // Check if we exceed max items limit
        if (count > config.maxItems) {
            alert(`Cannot perform bulk operation on more than ${config.maxItems} items. You have ${count} items selected.`);
            return;
        }

        // Show confirmation for large operations
        if (count >= config.confirmThreshold) {
            const message = action.confirmMessage || 
                           `Are you sure you want to ${action.label.toLowerCase()} ${count} items?`;
            if (!confirm(message)) {
                return;
            }
        } else if (action.confirm) {
            const message = action.confirmMessage || 
                           `Are you sure you want to ${action.label.toLowerCase()}?`;
            if (!confirm(message)) {
                return;
            }
        }

        await performGroupAction(action);
    };

    const performGroupAction = async (action) => {
        const count = selectedCount.value;
        const config = enterpriseGroupActionsConfig.value.bulkOperations;
        
        console.log(`Executing ${action.action} on ${count} items:`, selectedItems.value);
        
        // Show progress for large operations
        let progressCallback = null;
        if (config.showProgress && count >= config.confirmThreshold) {
            progressCallback = (processed, total) => {
                console.log(`Progress: ${processed}/${total} items processed`);
            };
        }

        // Emit event for parent to handle
        emit('group-action', {
            action: action.action,
            items: [...selectedItems.value],
            count: count,
            actionData: action,
            progressCallback,
            timestamp: new Date().toISOString()
        });
    };

    const clearSelection = () => {
        selectedItems.value = [];
    };

    const selectAll = (data) => {
        selectedItems.value = [...data];
    };

    const getActionIcon = (action) => {
        // Default icons for common actions
        const iconMap = {
            'delete': 'pi pi-trash',
            'export': 'pi pi-download',
            'archive': 'pi pi-archive',
            'approve': 'pi pi-check',
            'reject': 'pi pi-times',
            'duplicate': 'pi pi-copy',
            'move': 'pi pi-arrows-alt',
            'tag': 'pi pi-tag',
            'email': 'pi pi-envelope',
            'print': 'pi pi-print'
        };

        return action.icon || iconMap[action.action] || 'pi pi-cog';
    };

    const getActionSeverity = (action) => {
        // Default severities for common actions
        const severityMap = {
            'delete': 'danger',
            'export': 'info',
            'archive': 'secondary',
            'approve': 'success',
            'reject': 'danger',
            'duplicate': 'secondary',
            'move': 'warning',
            'tag': 'info',
            'email': 'info',
            'print': 'secondary'
        };

        return action.severity || severityMap[action.action] || 'secondary';
    };

    const getSelectionSummary = () => {
        const count = selectedCount.value;
        if (count === 0) return 'No items selected';
        if (count === 1) return '1 item selected';
        return `${count} items selected`;
    };

    const canExecuteAction = (action) => {
        const count = selectedCount.value;
        const config = enterpriseGroupActionsConfig.value.bulkOperations;
        
        if (count === 0) return false;
        if (count > config.maxItems) return false;
        if (action.requiresSelection && count === 0) return false;
        if (action.maxItems && count > action.maxItems) return false;
        if (action.minItems && count < action.minItems) return false;
        
        return true;
    };

    const batchProcess = async (action, batchSize = 50) => {
        const items = [...selectedItems.value];
        const batches = [];
        
        // Split items into batches
        for (let i = 0; i < items.length; i += batchSize) {
            batches.push(items.slice(i, i + batchSize));
        }
        
        // Process each batch
        for (let i = 0; i < batches.length; i++) {
            const batch = batches[i];
            
            emit('group-action', {
                action: action.action,
                items: batch,
                count: batch.length,
                actionData: action,
                batch: {
                    current: i + 1,
                    total: batches.length,
                    size: batchSize
                },
                timestamp: new Date().toISOString()
            });
            
            // Small delay between batches to prevent overwhelming the server
            if (i < batches.length - 1) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
        }
    };

    return {
        enterpriseGroupActionsConfig,
        hasGroupActions,
        hasSelectedItems,
        selectedCount,
        executeGroupAction,
        performGroupAction,
        clearSelection,
        selectAll,
        getActionIcon,
        getActionSeverity,
        getSelectionSummary,
        canExecuteAction,
        batchProcess
    };
}