import { computed } from 'vue';

export function useDataTableGroupingTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const subheaderRowGroupingDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Row Grouping Demo - Subheader Mode')
    );

    const columnGroupingDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Column Grouping Demo - Header & Footer Groups')
    );

    const rowspanRowGroupingDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'Row Grouping Demo - Rowspan Mode')
    );

    return {
        subheaderRowGroupingDataTable,
        columnGroupingDataTable,
        rowspanRowGroupingDataTable
    };
}