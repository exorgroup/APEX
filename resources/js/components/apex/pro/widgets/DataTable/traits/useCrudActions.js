// resources/js/components/apex/pro/widgets/DataTable/traits/useCrudActions.js

import { computed } from 'vue';

export function useCrudActions(props, emit) {
    const crudConfig = computed(() => ({
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
            }
        }
    }));

    const handleCrudAction = (action, data) => {
        const idField = crudConfig.value.crudActions.idField || 'id';
        const routes = crudConfig.value.crudActions.routes || {};
        
        // Check if there's a configured route
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
                data: data
            });
        }
    };

    const hasCrudActions = computed(() => {
        return crudConfig.value.showView || 
               crudConfig.value.showEdit || 
               crudConfig.value.showDelete || 
               crudConfig.value.showHistory || 
               crudConfig.value.showPrint;
    });

    return {
        crudConfig,
        handleCrudAction,
        hasCrudActions
    };
}