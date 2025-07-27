// resources/js/components/apex/core/widgets/DataTable/traits/useGridLines.js

import { computed } from 'vue';

export function useGridLines(props) {
    const gridLinesConfig = computed(() => ({
        showGridlines: props.showGridlines ?? true
    }));

    const tableClasses = computed(() => {
        const classes = [];
        if (props.gridLines === 'horizontal') classes.push('p-datatable-gridlines-horizontal');
        if (props.gridLines === 'vertical') classes.push('p-datatable-gridlines-vertical');
        if (props.gridLines === 'none') classes.push('p-datatable-gridlines-none');
        if (props.tableClass) classes.push(props.tableClass);
        return classes.join(' ');
    });

    return {
        gridLinesConfig,
        tableClasses
    };
}