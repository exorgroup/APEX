// resources/js/components/apex/enterprise/widgets/DataTable/composables/useCrudActions.js

import { computed } from 'vue';

export function useCrudActions(props, emit) {
    const enterpriseCrudConfig = computed(() => ({
        showView: props.showView ?? false,
        showEdit: props.showEdit ?? false,
        showDelete: props.showDelete ?? false,
        showHistory: props.showHistory ?? false,
        showPrint: props.showPrint ?? false,
        crudActions: props.crudActions ?? {
            idField: 'id',
            permissions: {
                view: true,
                edit: true,
                delete: true,
                history: true,
                print: true
            },
            routes: {},
            confirmations: {
                delete: true,
                deleteMessage: 'Are you sure you want to delete this item?'
            },
            callbacks: {
                beforeAction: null,
                afterAction: null
            }
        }
    }));

    const handleCrudAction = async (action, data) => {
        const config = enterpriseCrudConfig.value.crudActions;
        const idField = config.idField || 'id';
        
        // Check permissions
        if (config.permissions && config.permissions[action] === false) {
            console.warn(`Action '${action}' is not permitted`);
            return;
        }

        // Before action callback
        if (config.callbacks?.beforeAction) {
            const shouldContinue = await config.callbacks.beforeAction(action, data);
            if (shouldContinue === false) {
                return;
            }
        }

        // Handle confirmation for delete action
        if (action === 'delete' && config.confirmations?.delete) {
            const message = config.confirmations.deleteMessage || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                return;
            }
        }

        // Check if there's a configured route
        const routes = config.routes || {};
        const actionRoute = routes[action];
        
        if (actionRoute) {
            // Use configured route
            const url = actionRoute.replace(/{id}/g, data[idField]);
            window.location.href = url;
        } else {
            // Emit action event
            emit('crud-action', {
                action: action,
                id: data[idField],
                data: data,
                timestamp: new Date().toISOString()
            });
        }

        // After action callback
        if (config.callbacks?.afterAction) {
            config.callbacks.afterAction(action, data);
        }
    };

    const hasCrudActions = computed(() => {
        return enterpriseCrudConfig.value.showView || 
               enterpriseCrudConfig.value.showEdit || 
               enterpriseCrudConfig.value.showDelete || 
               enterpriseCrudConfig.value.showHistory || 
               enterpriseCrudConfig.value.showPrint;
    });

    const getActionColumns = computed(() => {
        const actions = [];
        const config = enterpriseCrudConfig.value;
        
        if (config.showView) {
            actions.push({
                field: '_action_view',
                header: '',
                action: 'view',
                icon: 'pi pi-eye',
                severity: 'success',
                tooltip: 'View'
            });
        }
        
        if (config.showEdit) {
            actions.push({
                field: '_action_edit',
                header: '',
                action: 'edit',
                icon: 'pi pi-pen-to-square',
                severity: 'info',
                tooltip: 'Edit'
            });
        }
        
        if (config.showDelete) {
            actions.push({
                field: '_action_delete',
                header: '',
                action: 'delete',
                icon: 'pi pi-eraser',
                severity: 'danger',
                tooltip: 'Delete'
            });
        }
        
        if (config.showHistory) {
            actions.push({
                field: '_action_history',
                header: '',
                action: 'history',
                icon: 'pi pi-history',
                severity: 'secondary',
                tooltip: 'History'
            });
        }
        
        if (config.showPrint) {
            actions.push({
                field: '_action_print',
                header: '',
                action: 'print',
                icon: 'pi pi-print',
                severity: 'help',
                tooltip: 'Print'
            });
        }
        
        return actions;
    });

    const isActionPermitted = (action) => {
        const permissions = enterpriseCrudConfig.value.crudActions.permissions || {};
        return permissions[action] !== false;
    };

    const bulkDelete = async (selectedItems) => {
        const config = enterpriseCrudConfig.value.crudActions;
        
        if (!isActionPermitted('delete')) {
            console.warn('Bulk delete is not permitted');
            return;
        }

        if (selectedItems.length === 0) {
            console.warn('No items selected for bulk delete');
            return;
        }

        const message = `Are you sure you want to delete ${selectedItems.length} items?`;
        if (config.confirmations?.delete && !confirm(message)) {
            return;
        }

        // Before action callback
        if (config.callbacks?.beforeAction) {
            const shouldContinue = await config.callbacks.beforeAction('bulk-delete', selectedItems);
            if (shouldContinue === false) {
                return;
            }
        }

        emit('crud-action', {
            action: 'bulk-delete',
            data: selectedItems,
            count: selectedItems.length,
            timestamp: new Date().toISOString()
        });

        // After action callback
        if (config.callbacks?.afterAction) {
            config.callbacks.afterAction('bulk-delete', selectedItems);
        }
    };

    return {
        enterpriseCrudConfig,
        handleCrudAction,
        hasCrudActions,
        getActionColumns,
        isActionPermitted,
        bulkDelete
    };
}