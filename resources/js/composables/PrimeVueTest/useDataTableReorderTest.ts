import { computed } from 'vue';

export function useDataTableReorderTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const reOrderDataTable = computed(() => 
        dataTableWidgets.value.find(w => w.props?.header?.title === 'ReOrder Feature Demo - NEWEST FUNCTIONALITY!')
    );

    return {
        reOrderDataTable
    };
}