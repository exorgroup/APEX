import { computed } from 'vue';

export function useDataTableBasicTest(widgets: any[]) {
    const dataTableWidgets = computed(() => 
        widgets.filter(w => w.type === 'datatable')
    );

    const basicDataTableWidgets = computed(() => 
        dataTableWidgets.value.filter(w => 
            w.props?.header?.title === 'Basic Products Table' ||
            w.props?.header?.title === 'Auto Mode with Smart Search'
        )
    );

    return {
        dataTableWidgets,
        basicDataTableWidgets
    };
}