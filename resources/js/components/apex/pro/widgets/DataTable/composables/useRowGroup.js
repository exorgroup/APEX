// resources/js/components/apex/pro/widgets/DataTable/composables/useRowGroup.js

import { computed } from 'vue';

export function useRowGroup(props) {
    const rowGroupConfig = computed(() => ({
        enabled: props.rowGrouping?.enabled ?? false,
        rowGroupMode: props.rowGrouping?.rowGroupMode ?? 'rowspan',
        groupRowsBy: props.rowGrouping?.groupRowsBy ?? [],
        sortField: props.rowGrouping?.sortField ?? undefined,
        sortOrder: props.rowGrouping?.sortOrder ?? 1,
        groupRowsTotals: props.rowGrouping?.groupRowsTotals ?? [],
        showHeaderTotal: props.rowGrouping?.showHeaderTotal ?? false,
        showHeaderRowCount: props.rowGrouping?.showHeaderRowCount ?? false,
        headerRowCountText: props.rowGrouping?.headerRowCountText ?? 'Items in this group: ',
        headerText: props.rowGrouping?.headerText ?? '',
        headerTemplate: props.rowGrouping?.headerTemplate ?? '',
        headerImageField: props.rowGrouping?.headerImageField ?? '',
        headerImageUrl: props.rowGrouping?.headerImageUrl ?? '',
        headerImagePosition: props.rowGrouping?.headerImagePosition ?? 'before',
        showFooterTotal: props.rowGrouping?.showFooterTotal ?? false,
        showFooterRowCount: props.rowGrouping?.showFooterRowCount ?? true,
        footerRowCountText: props.rowGrouping?.footerRowCountText ?? 'Total items: ',
        footerText: props.rowGrouping?.footerText ?? '',
        footerTemplate: props.rowGrouping?.footerTemplate ?? 'Total items: {rowCount}'
    }));

    const hasRowGrouping = computed(() => rowGroupConfig.value.enabled);

    const groupingField = computed(() => {
        if (!hasRowGrouping.value || !rowGroupConfig.value.groupRowsBy?.length) {
            return undefined;
        }
        return rowGroupConfig.value.groupRowsBy[0];
    });

    const groupingSortField = computed(() => {
        if (!hasRowGrouping.value) return undefined;
        return rowGroupConfig.value.sortField || groupingField.value;
    });

    const groupingSortOrder = computed(() => {
        if (!hasRowGrouping.value) return undefined;
        return rowGroupConfig.value.sortOrder || 1;
    });

    // Process template with placeholders
    const processTemplate = (template, data, extraParams = {}) => {
        if (!template) return '';
        
        return template.replace(/{(\w+(?:\.\w+)*)}/g, (match, fieldPath) => {
            // Handle special parameters like rowCount
            if (extraParams[fieldPath] !== undefined) {
                return String(extraParams[fieldPath]);
            }
            
            // Handle nested field paths like 'representative.name'
            const value = fieldPath.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, data);
            
            return String(value || '');
        });
    };

    const getGroupHeaderContent = (slotProps, data) => {
        if (!rowGroupConfig.value.enabled || rowGroupConfig.value.rowGroupMode !== 'subheader') {
            return { 
                imageUrl: '', 
                text: '', 
                imagePosition: 'before',
                showRowCount: false,
                rowCountText: '',
                customText: '',
                rowCount: 0
            };
        }
        
        const rowData = slotProps.data;
        let imageUrl = '';
        let text = '';
        
        // Determine image URL
        if (rowGroupConfig.value.headerImageField) {
            const imageFieldValue = rowGroupConfig.value.headerImageField.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, rowData);
            imageUrl = imageFieldValue || '';
        } else if (rowGroupConfig.value.headerImageUrl) {
            imageUrl = rowGroupConfig.value.headerImageUrl;
        }
        
        // Determine text content
        if (rowGroupConfig.value.headerTemplate) {
            text = processTemplate(rowGroupConfig.value.headerTemplate, rowData);
        } else if (groupingField.value) {
            const fieldValue = groupingField.value.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, rowData);
            text = String(fieldValue || '');
        }
        
        // Calculate row count for this group
        const groupValue = groupingField.value ? 
            groupingField.value.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, rowData) : '';
        const rowCount = calculateGroupRowCount(groupValue, data);
        
        return {
            imageUrl,
            text,
            imagePosition: rowGroupConfig.value.headerImagePosition,
            showRowCount: rowGroupConfig.value.showHeaderRowCount,
            rowCountText: rowGroupConfig.value.headerRowCountText,
            customText: rowGroupConfig.value.headerText,
            rowCount
        };
    };

    const getGroupFooterContent = (slotProps, data) => {
        if (!rowGroupConfig.value.enabled || rowGroupConfig.value.rowGroupMode !== 'subheader') {
            return {
                showRowCount: false,
                rowCountText: '',
                customText: '',
                rowCount: 0
            };
        }
        
        const rowData = slotProps.data;
        const groupValue = groupingField.value ? 
            groupingField.value.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, rowData) : '';
        
        // Calculate row count for this group
        const rowCount = calculateGroupRowCount(groupValue, data);
        
        // Calculate totals if specified
        const totals = {};
        if (rowGroupConfig.value.groupRowsTotals?.length) {
            rowGroupConfig.value.groupRowsTotals.forEach(field => {
                totals[field] = calculateGroupTotal(groupValue, field, data);
            });
        }
        
        // Process footer template if provided
        let processedFooterText = '';
        if (rowGroupConfig.value.footerTemplate) {
            processedFooterText = processTemplate(rowGroupConfig.value.footerTemplate, rowData, { 
                rowCount, 
                ...totals 
            });
        }
        
        return {
            showRowCount: rowGroupConfig.value.showFooterRowCount !== false,
            rowCountText: rowGroupConfig.value.footerRowCountText,
            customText: processedFooterText || rowGroupConfig.value.footerText,
            rowCount
        };
    };

    const calculateGroupRowCount = (groupValue, data) => {
        if (!groupingField.value || !data) return 0;
        
        return data.filter(item => {
            const itemGroupValue = groupingField.value.split('.').reduce((obj, key) => {
                return obj && obj[key] !== undefined ? obj[key] : '';
            }, item);
            return itemGroupValue === groupValue;
        }).length;
    };

    const calculateGroupTotal = (groupValue, field, data) => {
        if (!groupingField.value || !data) return 0;
        
        return data
            .filter(item => {
                const itemGroupValue = groupingField.value.split('.').reduce((obj, key) => {
                    return obj && obj[key] !== undefined ? obj[key] : '';
                }, item);
                return itemGroupValue === groupValue;
            })
            .reduce((sum, item) => {
                const fieldValue = field.split('.').reduce((obj, key) => {
                    return obj && obj[key] !== undefined ? obj[key] : 0;
                }, item);
                return sum + (parseFloat(fieldValue) || 0);
            }, 0);
    };

    return {
        rowGroupConfig,
        hasRowGrouping,
        groupingField,
        groupingSortField,
        groupingSortOrder,
        getGroupHeaderContent,
        getGroupFooterContent,
        calculateGroupRowCount,
        calculateGroupTotal,
        processTemplate
    };
}