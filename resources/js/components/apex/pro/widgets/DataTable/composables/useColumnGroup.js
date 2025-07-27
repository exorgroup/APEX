// resources/js/components/apex/pro/widgets/DataTable/composables/useColumnGroup.js

import { computed } from 'vue';

export function useColumnGroup(props) {
    const columnGroupConfig = computed(() => ({
        enabled: props.columnGrouping?.enabled ?? false,
        headerGroups: props.columnGrouping?.headerGroups ?? [],
        footerGroups: props.columnGrouping?.footerGroups ?? [],
        groupColumnsTotal: props.columnGrouping?.groupColumnsTotal ?? [],
        showTotalsInHeader: props.columnGrouping?.showTotalsInHeader ?? false,
        showTotalsInFooter: props.columnGrouping?.showTotalsInFooter ?? false,
        footerText: props.columnGrouping?.footerText ?? '',
        headerText: props.columnGrouping?.headerText ?? ''
    }));

    const hasHeaderGroups = computed(() => 
        columnGroupConfig.value.enabled && 
        columnGroupConfig.value.headerGroups && 
        columnGroupConfig.value.headerGroups.length > 0
    );

    const hasFooterGroups = computed(() => 
        columnGroupConfig.value.enabled && 
        columnGroupConfig.value.footerGroups && 
        columnGroupConfig.value.footerGroups.length > 0
    );

    // Calculate totals for column grouping
    const calculateColumnTotal = (field, type = 'sum', data) => {
        if (!data || data.length === 0) return 0;
        
        const values = data.map(item => {
            const value = field.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : 0;
            }, item);
            return parseFloat(value) || 0;
        }).filter(val => !isNaN(val));
        
        if (values.length === 0) return 0;
        
        switch (type) {
            case 'sum':
                return values.reduce((sum, val) => sum + val, 0);
            case 'avg':
                return values.reduce((sum, val) => sum + val, 0) / values.length;
            case 'count':
                return values.length;
            case 'min':
                return Math.min(...values);
            case 'max':
                return Math.max(...values);
            default:
                return values.reduce((sum, val) => sum + val, 0);
        }
    };

    // Format total values based on format type
    const formatColumnTotal = (value, formatType = 'number', decimals = 2) => {
        if (isNaN(value)) return '--NaN--';
        
        switch (formatType) {
            case 'currency':
                return value.toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            case 'percentage':
                return `${value.toFixed(decimals)}%`;
            case 'number':
                return value.toFixed(decimals);
            default:
                return String(value);
        }
    };

    // Process cell content for column groups
    const processCellContent = (cell, data) => {
        if (cell.header) {
            return processTemplate(cell.header, {}, {});
        }
        
        if (cell.footer) {
            return processTemplate(cell.footer, {}, {});
        }
        
        if (cell.isTotal && cell.totalField) {
            // Calculate and format total
            const total = calculateColumnTotal(cell.totalField, cell.totalType, data);
            return formatColumnTotal(total, cell.formatType, cell.formatDecimals);
        }
        
        return '';
    };

    // Process template with placeholders
    const processTemplate = (template, data, extraParams = {}) => {
        if (!template) return '';
        
        return template.replace(/{(\w+(?:\.\w+)*)}/g, (match, fieldPath) => {
            // Handle special parameters
            if (extraParams[fieldPath] !== undefined) {
                return String(extraParams[fieldPath]);
            }
            
            // Handle nested field paths
            const value = fieldPath.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, data);
            
            return String(value || '');
        });
    };

    return {
        columnGroupConfig,
        hasHeaderGroups,
        hasFooterGroups,
        calculateColumnTotal,
        formatColumnTotal,
        processCellContent,
        processTemplate
    };
}