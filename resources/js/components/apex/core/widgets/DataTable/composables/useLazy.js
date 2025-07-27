// resources/js/components/apex/core/widgets/DataTable/composables/useLazy.js

import { ref, computed } from 'vue';
import axios from 'axios';

export function useLazy(props) {
    const data = ref([]);
    const totalRecords = ref(0);
    const loading = ref(false);
    const isLazyMode = ref(false);

    // Initialize lazy mode
    if (props.dataSource?.lazy === true) {
        isLazyMode.value = true;
    } else if (props.dataSource?.lazy === false) {
        isLazyMode.value = false;
    }
    // For 'auto' mode, will be set in determineLazyMode()

    const lazyThreshold = computed(() => props.dataSource?.lazyThreshold || 1000);

    // Determine lazy mode for auto
    const determineLazyMode = async () => {
        if (!props.dataSource || props.dataSource.lazy !== 'auto') {
            return;
        }

        loading.value = true;
        
        try {
            // If a count URL is provided, use it
            if (props.dataSource.countUrl) {
                const response = await axios.get(props.dataSource.countUrl);
                const count = response.data.count || response.data.total || 0;
                isLazyMode.value = count > lazyThreshold.value;
                console.log(`Auto lazy mode: ${count} records, threshold: ${lazyThreshold.value}, using lazy: ${isLazyMode.value}`);
            } else {
                // Otherwise, try to fetch all data to check count
                const response = await axios({
                    method: props.dataSource.method || 'GET',
                    url: props.dataSource.url
                });
                
                const responseData = Array.isArray(response.data) ? response.data : (response.data.data || []);
                const count = response.data.total || responseData.length;
                
                // Determine based on count
                isLazyMode.value = count > lazyThreshold.value;
                console.log(`Auto lazy mode: ${count} records, threshold: ${lazyThreshold.value}, using lazy: ${isLazyMode.value}`);
                
                // If we're not using lazy mode, we already have the data
                if (!isLazyMode.value) {
                    data.value = responseData;
                    totalRecords.value = count;
                    loading.value = false;
                    return;
                }
            }
        } catch (error) {
            console.error('Error determining lazy mode:', error);
            // Default to lazy mode on error
            isLazyMode.value = true;
        }
        
        loading.value = false;
    };

    // Load data from server
    const loadData = async (event = null) => {
        if (props.staticData) {
            data.value = props.staticData;
            totalRecords.value = props.staticData.length;
            loading.value = false;
            return;
        }

        if (!props.dataSource?.url) return;

        loading.value = true;

        try {
            // Handle explicit lazy modes (true/false)
            if (props.dataSource.lazy === true || props.dataSource.lazy === false) {
                isLazyMode.value = props.dataSource.lazy;
            } else if (props.dataSource.lazy === 'auto') {
                await determineLazyMode();
                if (!isLazyMode.value && data.value.length > 0) {
                    return; // Data already loaded during auto detection
                }
            }

            // For non-lazy loading, always fetch all data
            if (!isLazyMode.value) {
                const response = await axios({
                    method: props.dataSource.method || 'GET',
                    url: props.dataSource.url
                });
                
                data.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
                totalRecords.value = data.value.length;
            } else {
                // Lazy loading with server-side processing
                const params = {
                    page: event?.page ?? 0,
                    first: event?.first ?? 0,
                    rows: event?.rows ?? 10,
                    sortField: event?.sortField,
                    sortOrder: event?.sortOrder
                };

                const response = await axios({
                    method: props.dataSource.method || 'GET',
                    url: props.dataSource.url,
                    params: props.dataSource.method === 'GET' ? params : undefined,
                    data: props.dataSource.method === 'POST' ? params : undefined
                });

                data.value = response.data.data || response.data;
                totalRecords.value = response.data.total || response.data.length;
            }
        } catch (error) {
            console.error('Error loading data:', error);
            data.value = [];
            totalRecords.value = 0;
        } finally {
            loading.value = false;
        }
    };

    return {
        data,
        totalRecords,
        loading,
        isLazyMode,
        loadData
    };
}