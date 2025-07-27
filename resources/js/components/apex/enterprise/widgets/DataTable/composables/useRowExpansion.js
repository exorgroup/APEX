// resources/js/components/apex/enterprise/widgets/DataTable/composables/useRowExpansion.js

import { ref, computed } from 'vue';

export function useRowExpansion(props, emit) {
    const expandedRows = ref({});

    const rowExpansionConfig = computed(() => ({
        enabled: props.rowExpansion?.enabled ?? false,
        expanderColumnStyle: props.rowExpansion?.expanderColumn?.style ?? 'width: 5rem',
        expanderColumnFrozen: props.rowExpansion?.expanderColumn?.frozen ?? false,
        showExpandControls: props.rowExpansion?.expandControls?.showExpandAll !== false,
        expandAllLabel: props.rowExpansion?.expandControls?.expandAllLabel ?? 'Expand All',
        collapseAllLabel: props.rowExpansion?.expandControls?.collapseAllLabel ?? 'Collapse All',
        expandControlsPosition: props.rowExpansion?.expandControls?.position ?? 'header',
        expandedContentType: props.rowExpansion?.expandedContent?.type ?? 'custom',
        title: props.rowExpansion?.expandedContent?.title ?? '',
        titleField: props.rowExpansion?.expandedContent?.titleField ?? '',
        titleTemplate: props.rowExpansion?.expandedContent?.titleTemplate ?? '',
        dataField: props.rowExpansion?.expandedContent?.dataField ?? '',
        widget: props.rowExpansion?.expandedContent?.widget ?? null,
        customTemplate: props.rowExpansion?.expandedContent?.customTemplate ?? '',
        onExpand: props.rowExpansion?.events?.onExpand ?? false,
        onCollapse: props.rowExpansion?.events?.onCollapse ?? false
    }));

    const hasRowExpansion = computed(() => rowExpansionConfig.value.enabled);

    const onRowExpand = (event) => {
        if (rowExpansionConfig.value.onExpand) {
            emit('row-expand', event);
        }
    };

    const onRowCollapse = (event) => {
        if (rowExpansionConfig.value.onCollapse) {
            emit('row-collapse', event);
        }
    };

    const expandAll = () => {
        if (!hasRowExpansion.value) return;
        
        // Expand all rows (assuming we have access to current data)
        const allRows = {};
        // This would need to be populated with actual row data
        // For now, we'll emit an event for the parent to handle
        emit('expand-all');
    };

    const collapseAll = () => {
        expandedRows.value = {};
        emit('collapse-all');
    };

    const getExpansionTitle = (rowData) => {
        const config = rowExpansionConfig.value;
        if (!config.enabled) return '';
        
        if (config.titleTemplate) {
            // Replace placeholders in template
            return config.titleTemplate.replace(/{(\w+)}/g, (match, field) => rowData[field] || '');
        } else if (config.title && config.titleField) {
            // Use title with field value
            return `${config.title} ${rowData[config.titleField]}`;
        } else if (config.title) {
            // Just use title
            return config.title;
        }
        
        return '';
    };

    const generateGUID = () => {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    };

    const getNestedWidgetConfig = (rowData) => {
        const config = rowExpansionConfig.value;
        if (!config.enabled || config.expandedContentType !== 'datatable' || !config.widget || !config.dataField) {
            return null;
        }
        
        // Get nested data from the specified field
        const nestedData = rowData[config.dataField] || [];
        
        // Create a copy of the widget configuration and set the data
        const widgetProps = { ...config.widget };
        
        // For nested tables, we typically want client-side mode
        if (widgetProps.dataSource) {
            widgetProps.dataSource = null; // Remove URL-based data source
        }
        
        const nestedWidgetProps = {
            ...widgetProps,
            // Pass the nested data directly
            staticData: nestedData,
            // Ensure nested table has a unique ID
            widgetId: `${props.widgetId}_expansion_${rowData[props.dataKey]}`
        };
        
        return {
            id: `nested_${generateGUID()}`,
            type: 'datatable',
            props: nestedWidgetProps
        };
    };

    const isRowExpanded = (rowData) => {
        return expandedRows.value[rowData[props.dataKey]] === true;
    };

    const toggleRowExpansion = (rowData) => {
        const key = rowData[props.dataKey];
        if (expandedRows.value[key]) {
            delete expandedRows.value[key];
        } else {
            expandedRows.value[key] = true;
        }
    };

    return {
        rowExpansionConfig,
        hasRowExpansion,
        expandedRows,
        onRowExpand,
        onRowCollapse,
        expandAll,
        collapseAll,
        getExpansionTitle,
        getNestedWidgetConfig,
        isRowExpanded,
        toggleRowExpansion
    };
}