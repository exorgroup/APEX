import { computed } from 'vue';

export function useDataTableAdvancedTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const reOrderDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'ReOrder Feature Demo - NEWEST FUNCTIONALITY!')
    );

    const conditionalStylingDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Products with Conditional Row/Cell Styling')
    );

    const clickableDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Products with Actions')
    );

    const filterDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Products with Individual Column Filters')
    );

    const autoModeDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Auto Mode with Smart Search')
    );

    return {
        dataTableWidgets,
        reOrderDataTable,
        conditionalStylingDataTable,
        clickableDataTable,
        filterDataTable,
        autoModeDataTable
    };
}