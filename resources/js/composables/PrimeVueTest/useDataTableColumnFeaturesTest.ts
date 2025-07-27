import { computed } from 'vue';

export function useDataTableColumnFeaturesTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const columnLockingDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Products with Column Locking - NEWEST FEATURE DEMO')
    );

    return {
        columnLockingDataTable
    };
}