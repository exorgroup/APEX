// resources/js/components/apex/enterprise/widgets/DataTable/composables/useContextMenu.js

import { ref, computed } from 'vue';

export function useContextMenu(props, emit) {
    const contextMenuSelection = ref(null);

    const contextMenuConfig = computed(() => ({
        enabled: props.contextMenu?.enabled ?? false,
        items: props.contextMenu?.items ?? []
    }));

    const hasContextMenu = computed(() => {
        return contextMenuConfig.value.enabled && contextMenuConfig.value.items?.length > 0;
    });

    // Process menu items with field replacements
    const processMenuItems = (rowData) => {
        if (!contextMenuConfig.value.items) return [];
        
        return contextMenuConfig.value.items
            .filter(item => item.visible !== false)
            .map(item => {
                // Process URL with field replacements
                let processedUrl = item.url;
                if (processedUrl) {
                    processedUrl = processedUrl.replace(/{(\w+)}/g, (match, field) => {
                        return rowData[field] || match;
                    });
                }
                
                // Determine icon type (URL vs PrimeIcon)
                const isIconUrl = item.icon && (item.icon.startsWith('http') || item.icon.startsWith('/') || item.icon.includes('.'));
                
                return {
                    label: item.label,
                    icon: !isIconUrl ? item.icon : undefined,
                    command: () => handleContextMenuAction(item, rowData, processedUrl),
                    separator: item.separator,
                    disabled: item.disabled,
                    class: isIconUrl ? 'p-menuitem-with-image' : undefined,
                    template: isIconUrl ? (options) => {
                        return `<a class="${options.class}" href="#">
                            <img src="${item.icon}" alt="${item.label}" class="p-menuitem-icon-image" />
                            <span class="p-menuitem-text">${item.label}</span>
                        </a>`;
                    } : undefined
                };
            });
    };

    // Handle context menu actions
    const handleContextMenuAction = (item, rowData, processedUrl) => {
        if (item.url && processedUrl) {
            // Navigate to URL
            window.open(processedUrl, item.urlTarget || '_self');
        } else if (item.action) {
            // Emit custom action
            emit('context-menu-action', {
                action: item.action,
                data: rowData,
                item: item
            });
        }
        
        // Clear context menu selection
        contextMenuSelection.value = null;
    };

    // Handle row context menu event
    const onRowContextMenu = (event) => {
        if (!hasContextMenu.value) return;
        
        // Set the selected row for context menu
        contextMenuSelection.value = event.data;
        
        // This would need to access the context menu ref to show it
        // The parent component needs to handle this
        emit('show-context-menu', {
            event: event.originalEvent,
            data: event.data
        });
    };

    // Context menu computed menu model
    const contextMenuModel = computed(() => {
        if (!hasContextMenu.value || !contextMenuSelection.value) return [];
        return processMenuItems(contextMenuSelection.value);
    });

    const clearContextMenuSelection = () => {
        contextMenuSelection.value = null;
    };

    return {
        contextMenuConfig,
        hasContextMenu,
        contextMenuSelection,
        contextMenuModel,
        processMenuItems,
        handleContextMenuAction,
        onRowContextMenu,
        clearContextMenuSelection
    };
}