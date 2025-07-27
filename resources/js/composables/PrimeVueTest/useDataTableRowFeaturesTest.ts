import { computed } from 'vue';

export function useDataTableRowFeaturesTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const rowExpansionDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Products with Order History - Row Expansion Demo')
    );

    const rowLockingDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Products with Row Locking - Latest Feature Demo')
    );

    return {
        rowExpansionDataTable,
        rowLockingDataTable
    };
}