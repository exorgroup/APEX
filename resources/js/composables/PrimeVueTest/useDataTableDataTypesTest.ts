import { computed } from 'vue';

export function useDataTableDataTypesTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const dataTypesDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Data Types Demo - Format Showcase')
    );

    return {
        dataTypesDataTable
    };
}