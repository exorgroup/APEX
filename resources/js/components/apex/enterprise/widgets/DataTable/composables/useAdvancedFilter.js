// resources/js/components/apex/enterprise/widgets/DataTable/composables/useAdvancedFilter.js

import { ref, computed } from 'vue';

export function useAdvancedFilter(props) {
    const filterBuilder = ref(null);
    const savedFilters = ref([]);
    const currentFilter = ref(null);

    const advancedFilterConfig = computed(() => ({
        enabled: props.advancedFilter?.enabled ?? false,
        filterBuilder: props.advancedFilter?.filterBuilder ?? false,
        savedFilters: props.advancedFilter?.savedFilters ?? false,
        customOperators: props.advancedFilter?.customOperators ?? []
    }));

    const hasAdvancedFilter = computed(() => {
        return advancedFilterConfig.value.enabled;
    });

    const hasFilterBuilder = computed(() => {
        return advancedFilterConfig.value.enabled && advancedFilterConfig.value.filterBuilder;
    });

    const hasSavedFilters = computed(() => {
        return advancedFilterConfig.value.enabled && advancedFilterConfig.value.savedFilters;
    });

    const buildFilter = (filterRules) => {
        // Build complex filter object from visual filter builder
        const filter = {
            rules: filterRules,
            operator: 'AND', // or 'OR'
            timestamp: Date.now()
        };
        
        currentFilter.value = filter;
        return filter;
    };

    const applyFilter = (filter) => {
        currentFilter.value = filter;
        // Apply the filter logic to the data
        console.log('Applying advanced filter:', filter);
    };

    const saveFilter = (name, filter) => {
        if (!hasSavedFilters.value) return;
        
        const savedFilter = {
            id: Date.now(),
            name,
            filter,
            createdAt: new Date().toISOString()
        };
        
        savedFilters.value.push(savedFilter);
        
        // Persist to localStorage
        localStorage.setItem(`advanced_filters_${props.widgetId}`, JSON.stringify(savedFilters.value));
    };

    const loadSavedFilters = () => {
        if (!hasSavedFilters.value) return;
        
        const saved = localStorage.getItem(`advanced_filters_${props.widgetId}`);
        if (saved) {
            try {
                savedFilters.value = JSON.parse(saved);
            } catch (error) {
                console.warn('Failed to load saved filters:', error);
                savedFilters.value = [];
            }
        }
    };

    const deleteFilter = (filterId) => {
        savedFilters.value = savedFilters.value.filter(f => f.id !== filterId);
        localStorage.setItem(`advanced_filters_${props.widgetId}`, JSON.stringify(savedFilters.value));
    };

    const clearFilter = () => {
        currentFilter.value = null;
    };

    const getAvailableOperators = () => {
        const defaultOperators = [
            { name: 'equals', label: 'Equals', operator: 'eq' },
            { name: 'not_equals', label: 'Not Equals', operator: 'ne' },
            { name: 'contains', label: 'Contains', operator: 'contains' },
            { name: 'starts_with', label: 'Starts With', operator: 'startsWith' },
            { name: 'ends_with', label: 'Ends With', operator: 'endsWith' },
            { name: 'less_than', label: 'Less Than', operator: 'lt' },
            { name: 'less_than_equal', label: 'Less Than or Equal', operator: 'lte' },
            { name: 'greater_than', label: 'Greater Than', operator: 'gt' },
            { name: 'greater_than_equal', label: 'Greater Than or Equal', operator: 'gte' },
            { name: 'in', label: 'In', operator: 'in' },
            { name: 'not_in', label: 'Not In', operator: 'notIn' }
        ];

        return [
            ...defaultOperators,
            ...advancedFilterConfig.value.customOperators
        ];
    };

    return {
        advancedFilterConfig,
        hasAdvancedFilter,
        hasFilterBuilder,
        hasSavedFilters,
        filterBuilder,
        savedFilters,
        currentFilter,
        buildFilter,
        applyFilter,
        saveFilter,
        loadSavedFilters,
        deleteFilter,
        clearFilter,
        getAvailableOperators
    };
}