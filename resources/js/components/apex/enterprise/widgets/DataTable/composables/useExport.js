// resources/js/components/apex/enterprise/widgets/DataTable/composables/useExport.js

import { computed } from 'vue';

export function useExport(props) {
    const exportConfig = computed(() => ({
        exportable: props.exportable ?? false,
        exportFormats: props.exportFormats ?? ['csv', 'excel', 'pdf'],
        exportFilename: props.exportFilename ?? 'data-export'
    }));

    const hasExport = computed(() => {
        return exportConfig.value.exportable && exportConfig.value.exportFormats.length > 0;
    });

    const exportData = (format, dt, visibleColumns, data, totalRecords) => {
        if (!dt?.value) {
            console.error('DataTable ref not available for export');
            return;
        }

        try {
            // Get visible and exportable columns
            const exportableColumns = visibleColumns.filter(col => 
                col.exportable !== false && 
                !col.field.startsWith('_action_') && 
                !col.field.startsWith('_lock_') && 
                !col.field.startsWith('_row_reorder')
            );
            
            // Use PrimeVue's built-in export methods
            switch (format) {
                case 'csv':
                    dt.value.exportCSV({
                        selectionOnly: false,
                        filename: `${exportConfig.value.exportFilename}.csv`
                    });
                    break;
                    
                case 'excel':
                    // Note: PrimeVue doesn't have built-in Excel export, so we'll use CSV as fallback
                    console.warn('Excel export not directly supported by PrimeVue, exporting as CSV');
                    dt.value.exportCSV({
                        selectionOnly: false,
                        filename: `${exportConfig.value.exportFilename}.csv`
                    });
                    break;
                    
                case 'pdf':
                    // Note: PrimeVue doesn't have built-in PDF export
                    console.warn('PDF export requires additional implementation');
                    exportToPDF(exportableColumns, data);
                    break;
                    
                default:
                    console.warn(`Unsupported export format: ${format}`);
            }
            
            console.log(`Exported ${format.toUpperCase()}:`, {
                filename: exportConfig.value.exportFilename,
                visibleColumns: exportableColumns.length,
                totalRows: totalRecords
            });
        } catch (error) {
            console.error(`Error exporting ${format}:`, error);
        }
    };

    // Basic PDF export implementation
    const exportToPDF = (columns, data) => {
        // This is a placeholder for PDF export functionality
        // In a real implementation, you would use a library like jsPDF
        console.log('PDF Export Data:', { columns, data });
        
        // Simple text-based export as fallback
        const csvContent = generateCSVContent(columns, data);
        downloadAsFile(csvContent, `${exportConfig.value.exportFilename}.txt`, 'text/plain');
    };

    // Generate CSV content
    const generateCSVContent = (columns, data) => {
        const headers = columns.map(col => col.header).join(',');
        const rows = data.map(row => 
            columns.map(col => {
                const value = row[col.field];
                // Escape commas and quotes in CSV
                if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
                    return `"${value.replace(/"/g, '""')}"`;
                }
                return value || '';
            }).join(',')
        );
        
        return [headers, ...rows].join('\n');
    };

    // Download file helper
    const downloadAsFile = (content, filename, mimeType) => {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    };

    // Export selected data only
    const exportSelected = (format, selectedData, dt, visibleColumns) => {
        if (!selectedData || selectedData.length === 0) {
            console.warn('No data selected for export');
            return;
        }

        // Use the same export logic but with selected data
        exportData(format, dt, visibleColumns, selectedData, selectedData.length);
    };

    // Get export statistics
    const getExportStats = (visibleColumns, data) => {
        const exportableColumns = visibleColumns.filter(col => 
            col.exportable !== false && 
            !col.field.startsWith('_action_') && 
            !col.field.startsWith('_lock_') && 
            !col.field.startsWith('_row_reorder')
        );

        return {
            totalColumns: visibleColumns.length,
            exportableColumns: exportableColumns.length,
            totalRows: data.length,
            exportFormats: exportConfig.value.exportFormats
        };
    };

    return {
        exportConfig,
        hasExport,
        exportData,
        exportSelected,
        generateCSVContent,
        downloadAsFile,
        getExportStats
    };
}