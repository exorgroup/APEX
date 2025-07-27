// resources/js/components/apex/pro/widgets/DataTable/composables/useReorder.js

import { computed } from 'vue';

export function useReorder(props, emit) {
    const reorderConfig = computed(() => ({
        reorderableColumns: props.reorderableColumns ?? false,
        reorderableRows: props.reorderableRows ?? false,
        reOrder: props.reOrder ?? {
            enabled: false,
            reOrderColumn: false,
            reOrderRows: false,
            excludeOrdering: ''
        }
    }));

    const hasReOrder = computed(() => reorderConfig.value.reOrder?.enabled ?? false);

    const isColumnReorderEnabled = computed(() => {
        return hasReOrder.value && reorderConfig.value.reOrder?.reOrderColumn;
    });

    const isRowReorderEnabled = computed(() => {
        return hasReOrder.value && reorderConfig.value.reOrder?.reOrderRows;
    });

    // Parse exclude ordering list
    const excludedFromOrdering = computed(() => {
        if (!reorderConfig.value.reOrder?.excludeOrdering) return new Set();
        
        return new Set(
            reorderConfig.value.reOrder.excludeOrdering
                .split(',')
                .map(field => field.trim())
                .filter(field => field.length > 0)
        );
    });

    // Check if a column should be reorderable
    const isColumnReorderable = (column) => {
        if (!isColumnReorderEnabled.value) return false;
        if (excludedFromOrdering.value.has(column.field)) return false;
        if (column.frozen) return false;
        if (column.field.startsWith('_action_') || column.field.startsWith('_lock_')) return false;
        return column.reorderable !== false;
    };

    // Get effective reorderableColumns setting for DataTable
    const effectiveReorderableColumns = computed(() => {
        return isColumnReorderEnabled.value && props.columns.some(col => isColumnReorderable(col));
    });

    // Get effective reorderableRows setting for DataTable
    const effectiveReorderableRows = computed(() => {
        return isRowReorderEnabled.value;
    });

    const onColumnReorder = (event) => {
        console.log('Column reorder:', event);
        emit('column-reorder', event);
    };

    const onRowReorder = (event, data) => {
        // Update data with new order
        if (data) {
            data.value = event.value;
        }
        console.log('Row reorder:', event);
        emit('row-reorder', event);
    };

    return {
        reorderConfig,
        hasReOrder,
        isColumnReorderEnabled,
        isRowReorderEnabled,
        excludedFromOrdering,
        isColumnReorderable,
        effectiveReorderableColumns,
        effectiveReorderableRows,
        onColumnReorder,
        onRowReorder
    };
}