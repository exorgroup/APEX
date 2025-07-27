// resources/js/components/apex/pro/widgets/DataTable/composables/useFrozenColumns.js

import { ref, computed } from 'vue';

export function useFrozenColumns(props, emit) {
    const lockedColumnFields = ref(new Set());

    const frozenColumnsConfig = computed(() => ({
        frozenColumns: props.frozenColumns ?? 0,
        columnLocking: props.columnLocking ?? {
            enabled: false,
            buttonPosition: 'toolbar',
            buttonStyle: '',
            buttonClass: ''
        }
    }));

    // Initialize locked columns based on lockColumn property
    const initializeLockedColumns = () => {
        const initialLockedFields = new Set();
        props.columns.forEach(col => {
            if (col.lockColumn) {
                initialLockedFields.add(col.field);
            }
        });
        lockedColumnFields.value = initialLockedFields;
    };

    // Check if a column is currently locked
    const isColumnLocked = (field) => {
        return lockedColumnFields.value.has(field);
    };

    // Toggle column lock state
    const toggleColumnLock = (field) => {
        const newLockedFields = new Set(lockedColumnFields.value);
        
        if (newLockedFields.has(field)) {
            newLockedFields.delete(field);
        } else {
            newLockedFields.add(field);
        }
        
        lockedColumnFields.value = newLockedFields;
        
        // Emit event for parent to handle
        emit('column-lock-change', {
            field: field,
            locked: newLockedFields.has(field),
            allLockedFields: Array.from(newLockedFields)
        });
    };

    // Get columns that have lockButton enabled
    const lockableColumns = computed(() => {
        return props.columns.filter(col => col.lockButton);
    });

    // Check if column locking is enabled and has lockable columns
    const hasColumnLocking = computed(() => {
        return frozenColumnsConfig.value.columnLocking?.enabled && lockableColumns.value.length > 0;
    });

    // Get column lock button position
    const columnLockButtonPosition = computed(() => {
        return frozenColumnsConfig.value.columnLocking?.buttonPosition || 'toolbar';
    });

    return {
        frozenColumnsConfig,
        lockedColumnFields,
        initializeLockedColumns,
        isColumnLocked,
        toggleColumnLock,
        lockableColumns,
        hasColumnLocking,
        columnLockButtonPosition
    };
}