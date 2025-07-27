// resources/js/components/apex/pro/widgets/DataTable/composables/useFrozenRows.js

import { ref, computed } from 'vue';

export function useFrozenRows(props, emit) {
    const lockedRows = ref([]);

    const frozenRowsConfig = computed(() => ({
        enabled: props.rowLocking?.enabled ?? false,
        maxLockedRows: props.rowLocking?.maxLockedRows ?? 5,
        lockColumn: props.rowLocking?.lockColumn ?? {
            style: 'width: 4rem',
            frozen: false,
            header: ''
        },
        lockedRowClasses: props.rowLocking?.lockedRowClasses ?? 'font-bold',
        lockedRowStyles: props.rowLocking?.lockedRowStyles ?? {}
    }));

    const toggleRowLock = (rowData, isLocked, index) => {
        if (!frozenRowsConfig.value.enabled) return;
        
        const dataKey = props.dataKey;
        const maxLocked = frozenRowsConfig.value.maxLockedRows;
        
        if (isLocked) {
            // Unlock the row
            const lockedIndex = lockedRows.value.findIndex(row => row[dataKey] === rowData[dataKey]);
            if (lockedIndex !== -1) {
                const unlockedRow = lockedRows.value.splice(lockedIndex, 1)[0];
                emit('row-unlock', { row: unlockedRow, index: lockedIndex });
            }
        } else {
            // Lock the row - check limit first
            if (lockedRows.value.length >= maxLocked) {
                console.warn(`Maximum ${maxLocked} rows can be locked`);
                return;
            }
            
            lockedRows.value.push(rowData);
            emit('row-lock', { row: rowData, index });
        }
    };

    const isRowLocked = (rowData) => {
        if (!frozenRowsConfig.value.enabled) return false;
        const dataKey = props.dataKey;
        return lockedRows.value.some(row => row[dataKey] === rowData[dataKey]);
    };

    const isMaxLockedRowsReached = computed(() => {
        if (!frozenRowsConfig.value.enabled) return false;
        return lockedRows.value.length >= frozenRowsConfig.value.maxLockedRows;
    });

    const lockedRowsCount = computed(() => lockedRows.value.length);

    return {
        frozenRowsConfig,
        lockedRows,
        toggleRowLock,
        isRowLocked,
        isMaxLockedRowsReached,
        lockedRowsCount
    };
}