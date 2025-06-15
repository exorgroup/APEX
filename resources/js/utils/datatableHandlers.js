// resources/js/utils/datatableHandlers.js

// Example client-side handlers for DataTable actions
// These would typically be imported and used in your Vue components

export const handleEdit = (rowData) => {
    console.log('Edit clicked for:', rowData);
    // Implement your edit logic here
    // For example, open a modal or navigate to an edit page
    alert(`Editing user: ${rowData.name}`);
};

export const handleDelete = (rowData) => {
    console.log('Delete clicked for:', rowData);
    if (confirm(`Are you sure you want to delete ${rowData.name}?`)) {
        // Implement delete logic here
        console.log('Deleting user:', rowData.id);
    }
};

export const handleView = (rowData) => {
    console.log('View clicked for:', rowData);
    // Implement view logic here
    alert(`Viewing details for: ${rowData.name}\nEmail: ${rowData.email}\nRole: ${rowData.role}`);
};

export const handleExport = (format, data) => {
    console.log(`Exporting data as ${format}`, data);
    // Implement export logic here
    // This could involve calling a server endpoint or using a client-side library
};

export const handleBulkDelete = (selectedRows) => {
    console.log('Bulk delete for:', selectedRows);
    if (selectedRows.length === 0) {
        alert('Please select at least one row');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedRows.length} items?`)) {
        // Implement bulk delete logic here
        console.log('Deleting users:', selectedRows.map(row => row.id));
    }
};

export const calculateSubtotal = (groupData, field) => {
    // Calculate subtotal for a grouped column
    return groupData.reduce((sum, item) => sum + (item[field] || 0), 0);
};

// Make functions available globally for the DataTable widget
if (typeof window !== 'undefined') {
    window.handleEdit = handleEdit;
    window.handleDelete = handleDelete;
    window.handleView = handleView;
    window.handleExport = handleExport;
    window.handleBulkDelete = handleBulkDelete;
    window.calculateSubtotal = calculateSubtotal;
}